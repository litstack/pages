<?php

namespace Tests;

use Litstack\Pages\PagesServiceProvider;
use Litstack\Rehearsal\TestCase as LitstackTestCase;
use ReflectionClass;
use ReflectionProperty;

class TestCase extends LitstackTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('vendor:publish', [
            '--provider' => PagesServiceProvider::class,
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            PagesServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * Calling protected or private class method.
     *
     * @param  mixed|string $abstract
     * @param  string       $method
     * @param  array        $params
     * @return mixed
     */
    protected function callUnaccessibleMethod($abstract, string $method, array $params = [])
    {
        $class = $abstract;
        if (! is_string($abstract)) {
            $class = get_class($abstract);
        }

        $class = new ReflectionClass($class);
        $method = $class->getMethod($method);
        $method->setAccessible(true);

        if ($method->isStatic()) {
            return $method->invokeArgs(null, $params);
        }

        return $method->invokeArgs($abstract, $params);
    }

    /**
     * Set protected or private class property value.
     *
     * @param  mixed  $instance
     * @param  string $property
     * @param  mixed  $value
     * @return void
     */
    public function setUnaccessibleProperty($instance, string $property, $value)
    {
        $reflection = new ReflectionProperty(get_class($instance), $property);
        $reflection->setAccessible(true);
        $value = $reflection->setValue($instance, $value);
    }

    /**
     * Get protected or private class property value.
     *
     * @param  mixed  $instance
     * @param  string $property
     * @param  mixed  $value
     * @return mixed
     */
    public function getUnaccessibleProperty($instance, string $property)
    {
        $reflection = new ReflectionProperty(get_class($instance), $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($instance);
    }

    /**
     * Assert class has trait.
     *
     * @param  string       $trait
     * @param  string|mixed $class
     * @return void
     */
    public function assertHasTrait(string $trait, $class)
    {
        $traits = array_flip(class_uses_recursive($class));
        $this->assertArrayHasKey($trait, $traits);
    }
}
