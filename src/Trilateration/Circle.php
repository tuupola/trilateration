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

class Circle extends Point
{
    protected $radius;

    public function __construct($latitude, $longitude, $radius)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->radius = $radius;
    }

    public function radius()
    {
        return $this->radius;
    }


    public function __toString()
    {
        return "{$this->latitude},{$this->longitude},{$this->radius}";
    }
}
