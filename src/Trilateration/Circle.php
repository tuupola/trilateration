<?php

namespace Tuupola\Trilateration;

use Tuupola\Trilateration;
use Nubs\Vectorix\Vector;

class Circle extends Point
{
    protected $distance;

    public function __construct($latitude, $longitude, $distance)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->distance = $distance;
    }

    public function distance()
    {
        return $this->distance;
    }
}
