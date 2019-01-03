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

use PHPUnit\Framework\TestCase;

class NonLinearLeastSquaresTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldConstructWithThreeSpheres()
    {
        $sphere1 = new Sphere(7.975332, 98.339406, 10);
        $sphere2 = new Sphere(7.975332, 98.339906, 30);
        $sphere3 = new Sphere(7.975492, 98.339296, 20);

        $trilateration = new NonLinearLeastSquares($sphere1, $sphere2, $sphere3);
        $this->assertInstanceOf(NonLinearLeastSquares::class, $trilateration);

        /* Closure kludge to test private properties. */
        $self = $this;
        $closure = function () use ($self) {
            $self->assertEquals(3, count($this->spheres));
        };
        call_user_func($closure->bindTo($trilateration, NonLinearLeastSquares::class));
    }

    public function testShouldAddSphere()
    {
        $sphere1 = new Sphere(7.975332, 98.339406, 10);
        $sphere2 = new Sphere(7.975332, 98.339906, 30);
        $sphere3 = new Sphere(7.975492, 98.339296, 20);
        $sphere4 = new Sphere(7.974592, 98.339996, 25);

        $trilateration = new NonLinearLeastSquares($sphere1, $sphere2, $sphere3);
        $trilateration = $trilateration->addSphere($sphere4);

        /* Closure kludge to test private properties. */
        $self = $this;
        $closure = function () use ($self) {
            $self->assertEquals(4, count($this->spheres));
        };
        call_user_func($closure->bindTo($trilateration, NonLinearLeastSquares::class));
    }

    public function testShouldBeImmutable()
    {
        $sphere1 = new Sphere(7.975332, 98.339406, 10);
        $sphere2 = new Sphere(7.975332, 98.339906, 30);
        $sphere3 = new Sphere(7.975492, 98.339296, 20);
        $sphere4 = new Sphere(7.974592, 98.339996, 25);

        $trilateration = new NonLinearLeastSquares($sphere1, $sphere2, $sphere3);
        $trilateration2 = $trilateration->addSphere($sphere4);

        /* Closure kludge to test private properties. */
        $self = $this;
        $closure = function () use ($self) {
            $self->assertEquals(3, count($this->spheres));
        };
        call_user_func($closure->bindTo($trilateration, NonLinearLeastSquares::class));

        $closure = function () use ($self) {
            $self->assertEquals(4, count($this->spheres));
        };
        call_user_func($closure->bindTo($trilateration2, NonLinearLeastSquares::class));
    }

    public function testShouldFindTallinn()
    {
        $sphere1 = new Sphere(60.1695, 24.9354, 82175);
        $sphere2 = new Sphere(58.3806, 26.7251, 163311);
        $sphere3 = new Sphere(58.3859, 24.4971, 117932);
        $sphere4 = new Sphere(7.974592, 98.339996, 25);

        $trilateration = new NonLinearLeastSquares($sphere1, $sphere2, $sphere3);
        $point = $trilateration->position();

        $this->assertEquals(59.4366, round($point->latitude(), 4));
        $this->assertEquals(24.7589, round($point->longitude(), 4));
    }
}
