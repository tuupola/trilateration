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

        $nls = new NonLinearLeastSquares($sphere1, $sphere2, $sphere3);
        $this->assertInstanceOf(NonLinearLeastSquares::class, $nls);

        /* Closure kludge to test private properties. */
        $self = $this;
        $closure = function () use ($self) {
            $self->assertEquals(3, count($this->spheres));
        };
        call_user_func($closure->bindTo($nls, NonLinearLeastSquares::class));
    }

    public function testShouldAddSphere()
    {
        $sphere1 = new Sphere(7.975332, 98.339406, 10);
        $sphere2 = new Sphere(7.975332, 98.339906, 30);
        $sphere3 = new Sphere(7.975492, 98.339296, 20);
        $sphere4 = new Sphere(7.974592, 98.339996, 25);

        $nls = new NonLinearLeastSquares($sphere1, $sphere2, $sphere3);
        $nls = $nls->addSphere($sphere4);

        /* Closure kludge to test private properties. */
        $self = $this;
        $closure = function () use ($self) {
            $self->assertEquals(4, count($this->spheres));
        };
        call_user_func($closure->bindTo($nls, NonLinearLeastSquares::class));
    }

    public function testShouldBeImmutable()
    {
        $sphere1 = new Sphere(7.975332, 98.339406, 10);
        $sphere2 = new Sphere(7.975332, 98.339906, 30);
        $sphere3 = new Sphere(7.975492, 98.339296, 20);
        $sphere4 = new Sphere(7.974592, 98.339996, 25);

        $nls = new NonLinearLeastSquares($sphere1, $sphere2, $sphere3);
        $nls2 = $nls->addSphere($sphere4);

        /* Closure kludge to test private properties. */
        $self = $this;
        $closure = function () use ($self) {
            $self->assertEquals(3, count($this->spheres));
        };
        call_user_func($closure->bindTo($nls, NonLinearLeastSquares::class));

        $closure = function () use ($self) {
            $self->assertEquals(4, count($this->spheres));
        };
        call_user_func($closure->bindTo($nls2, NonLinearLeastSquares::class));
    }
}
