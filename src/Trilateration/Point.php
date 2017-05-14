<?php

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

    public function toVector()
    {
        $vx = Trilateration::EARTH_RADIUS * (cos(deg2rad($this->latitude()))
            * cos(deg2rad($this->longitude())));
        $vy = Trilateration::EARTH_RADIUS * (cos(deg2rad($this->latitude()))
            * sin(deg2rad($this->longitude())));
        $vz = Trilateration::EARTH_RADIUS * (sin(deg2rad($this->latitude())));

        return new Vector([$vx, $vy, $vz]);
    }
}