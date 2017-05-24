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

use Kachkaev\PHPR\RCore;
use Kachkaev\PHPR\Engine\CommandLineREngine;
use Kachkaev\PHPR\ROutputParser;
use Tuupola\Trilateration\Sphere;
use Tuupola\Trilateration\Point;

class NonLinearLeastSquares
{
    private $spheres = [];

    public function __construct(Sphere ...$spheres)
    {
        $this->spheres = $spheres;
    }

    public function position()
    {
        $latitude = array_map(function ($sphere) {
            return $sphere->latitude();
        }, $this->spheres);
        $latitude = implode($latitude, ",");

        $longitude = array_map(function ($sphere) {
            return $sphere->longitude();
        }, $this->spheres);
        $longitude = implode($longitude, ",");

        $distance = array_map(function ($sphere) {
            return $sphere->radius();
        }, $this->spheres);
        $distance = implode($distance, ",");

        $r = (new RCore(new CommandLineREngine("/usr/local/bin/R")))->createInteractiveProcess();
        $r->start();
        $r->write(<<<EOF
# install.packages("geosphere")
library(geosphere)

locations <- data.frame(
    latitude = c($latitude),
    longitude = c($longitude),
    distance = c($distance)
)
EOF
        );

        $r->write(<<<'EOF'
# Use average as the starting point
fit <- nls(
    distance ~ distm(data.frame(longitude, latitude), c(fitLongitude, fitLatitude)),
    data = locations,
    start=list(fitLongitude=mean(locations$longitude), fitLatitude=mean(locations$latitude)),
    control=list(maxiter=1000, tol=1e-02, minFactor=1/2048)
)

# Shortcut to result
longitude <- summary(fit)$coefficients[1]
latitude <- summary(fit)$coefficients[2]

print(paste(latitude, longitude, sep=","))
EOF
        );

        $output = trim($r->getLastWriteOutput());
        $output = str_replace(['[1] ', '"'], "", $output);
        list($latitude, $longitude) = explode(",", $output);
        return new Point($latitude, $longitude);
    }
}
