<?php

/*
 * This file is part of trilateration package
 *
 * Copyright (c) 2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/trilateration
 *
 */

namespace Tuupola\Trilateration;

use Nubs\Vectorix\Vector;
use Tuupola\Trilateration\Sphere;
use Tuupola\Trilateration\Point;

use RuntimeException;

class Intersection
{
    const EARTH_RADIUS = 6378137;

    private $autocorrect = 1;
    private $spheres = [];

    public function __construct(Sphere ...$spheres)
    {
        $this->spheres = $spheres;
    }

    public function addSphere(Sphere $sphere)
    {
        $spheres = array_merge([$sphere], $this->spheres);
        return new self(...$spheres);
    }

    public function position()
    {
        $point = $this->intersection();

        /* If autocorrect is set increase sphere radius until they intersect. */
        if ($this->autocorrect) {
            while (!$point) {
                $this->spheres[0] = $this->spheres[0]->enlarge($this->autocorrect);
                $this->spheres[1] = $this->spheres[1]->enlarge($this->autocorrect);
                $this->spheres[2] = $this->spheres[2]->enlarge($this->autocorrect);
                $point = $this->intersection();
            }
        }

        /* If solution not found throw. */
        if (!$point) {
            throw new RuntimeException("Spheres do not intersect.");
        }

        return $point;
    }

    private function intersection()
    {
        /* http://en.wikipedia.org/wiki/Trilateration */
        $P1 = $this->spheres[0]->toEarthCenteredVector();
        $P2 = $this->spheres[1]->toEarthCenteredVector();
        $P3 = $this->spheres[2]->toEarthCenteredVector();

        /* $ex is the unit vector in the direction from P1 to P2. */
        $ex = $P2->subtract($P1)->normalize();
        /* $i is the signed magnitude of the x component, in the figure 1  */
        /* coordinate system, of the vector from P1 to P3. */
        $i = $ex->dotProduct($P3->subtract($P1));
        /* $ey is the unit vector in the y direction. Note that the points P1, P2 */
        /* and P3 are all in the z = 0 plane of the figure 1 coordinate system. */
        $temp = $ex->multiplyByScalar($i);
        $ey = $P3->subtract($P1)->subtract($temp)->normalize();
        /* $ez is third basis vector. */
        $ez = $ex->crossProduct($ey);
        /* $d is the distance between the centers P1 and P2. */
        $d = $P2->subtract($P1)->length();
        /* $j is the signed magnitude of the y component, in the figure 1 */
        /* coordinate system, of the vector from P1 to P3. */
        $j = $ey->dotProduct($P3->subtract($P1));

        $x = (
            pow($this->spheres[0]->radius(), 2) -
            pow($this->spheres[1]->radius(), 2) +
            pow($d, 2)
        ) / (2 * $d);

        $y = ((
            pow($this->spheres[0]->radius(), 2) -
            pow($this->spheres[2]->radius(), 2) +
            pow($i, 2) + pow($j, 2)
        ) / (2 * $j)) - (($i / $j) * $x);

        /* If $z = NaN if circle does not touch sphere. No solution. */
        /* If $z = 0 circle touches sphere at exactly one point. */
        /* If $z < 0 > z circle touches sphere at two points. */
        $z = sqrt(pow($this->spheres[0]->radius(), 2) - pow($x, 2) - pow($y, 2));
        /* Using absolute value makes formula pass even when circles do not */
        /* overlap. The result, however is not correct. */
        //$z = sqrt(abs(pow($this->spheres[0]->radius(), 2) - pow($x, 2) - pow($y, 2)));

        if (is_nan($z)) {
            return false;
        }

        /* triPt is vector with ECEF x,y,z of trilateration point */
        $triPt = $P1
            ->add($ex->multiplyByScalar($x))
            ->add($ey->multiplyByScalar($y))
            ->add($ez->multiplyByScalar($z));

        $triPtX = $triPt->components()[0];
        $triPtY = $triPt->components()[1];
        $triPtZ = $triPt->components()[2];

        /* Convert back to lat/long from ECEF. Convert to degrees. */
        $latitude = rad2deg(asin($triPtZ / self::EARTH_RADIUS));
        $longitude = rad2deg(atan2($triPtY, $triPtX));

        return new Point($latitude, $longitude);
    }
}
