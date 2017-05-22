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

use Tuupola\Trilateration;
use Nubs\Vectorix\Vector;

class Point
{
    protected $latitude;
    protected $longitude;

    public function __construct($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function latitude()
    {
        return $this->latitude;
    }

    public function longitude()
    {
        return $this->longitude;
    }

    public function toEarthCenteredVector()
    {
        $vx = Trilateration::EARTH_RADIUS * (cos(deg2rad($this->latitude()))
            * cos(deg2rad($this->longitude())));
        $vy = Trilateration::EARTH_RADIUS * (cos(deg2rad($this->latitude()))
            * sin(deg2rad($this->longitude())));
        $vz = Trilateration::EARTH_RADIUS * (sin(deg2rad($this->latitude())));

        return new Vector([$vx, $vy, $vz]);
    }

    public function distance(Point $point)
    {
        $lat1 = $this->latitude();
        $lon1 = $this->longitude();
        $lat2 = $point->latitude();
        $lon2 = $point->longitude();

        $latd = deg2rad($lat2 - $lat1);
        $lond = deg2rad($lon2 - $lon1);
        $a = sin($latd / 2) * sin($latd / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lond / 2) * sin($lond / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return Trilateration::EARTH_RADIUS * $c;
    }

    public function __toString()
    {
        return "{$this->latitude},{$this->longitude}";
    }
}
