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

namespace Tuupola;

use Nubs\Vectorix\Vector;
use Tuupola\Trilateration\Circle;
use Tuupola\Trilateration\Point;

class Trilateration
{
    const EARTH_RADIUS = 6378137;

    private $circleA;
    private $circleB;
    private $circleC;

    public function __construct(
        Circle $circleA,
        Circle $circleB,
        Circle $circleC
    ) {
        $this->circleA = $circleA;
        $this->circleB = $circleB;
        $this->circleC = $circleC;
    }

    public function position()
    {
        $point1 = $this->intersection();
        $trilateration = new Trilateration($this->circleB, $this->circleA, $this->circleC);
        $point2 = $trilateration->intersection();

        return new Point(
            ($point1->latitude() + $point2->latitude()) / 2,
            ($point1->longitude() + $point2->longitude()) / 2
        );
    }

    public function intersection()
    {
        /* http://en.wikipedia.org/wiki/Trilateration */
        /* https://gis.stackexchange.com/a/415 */
        /* https://gist.github.com/dav-/bb7103008cdf9359887f */
        /* https://github.com/prbdias/trilateration */

        $P1 = $this->circleA->toEarthCenteredVector();
        $P2 = $this->circleB->toEarthCenteredVector();
        $P3 = $this->circleC->toEarthCenteredVector();

        $ex = $P2->subtract($P1)->normalize();
        $i = $ex->dotProduct($P3->subtract($P1));
        $temp = $ex->multiplyByScalar($i);
        $ey = $P3->subtract($P1)->subtract($temp)->normalize();
        $ez = $ex->crossProduct($ey);
        $d = $P2->subtract($P1)->length();
        $j = $ey->dotProduct($P3->subtract($P1));

        $x = (
            pow($this->circleA->radius(), 2) -
            pow($this->circleB->radius(), 2) +
            pow($d, 2)
        ) / (2 * $d);

        $y = ((
            pow($this->circleA->radius(), 2) -
            pow($this->circleC->radius(), 2) +
            pow($i, 2) + pow($j, 2)
        ) / (2 * $j)) - (($i / $j) * $x);

        /* If z = NaN if circle does not touch sphere. No solution. */
        /* If z = 0 circle touches sphere at exactly one point. */
        /* If z < 0 > z circle touches sphere at two points. */
        $z = sqrt(abs(pow($this->circleA->radius(), 2) - pow($x, 2) - pow($y, 2)));

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
