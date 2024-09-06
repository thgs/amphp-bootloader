<?php

namespace Tests\Unit\DependencyInjection;

use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use thgs\Bootstrap\DependencyInjection\IlluminateInjector;

class IlluminateContainerTest extends TestCase
{
    public function testCanRegister()
    {
        $subject = new IlluminateInjector(Container::getInstance());

        $instance = new TestInstance();
        $subject->register($instance);

        $this->assertSame($instance, $subject->create(TestInstance::class));
    }
}


class TestInstance
{
}
