<?php

namespace Tests;

use FjordPages\Models\FjordPage;
use Illuminate\Support\Facades\Request;
use Mockery as m;

class FjordPageModelTest extends TestCase
{
    /** @test */
    public function test_current_method_doesnt_fail_when_no_route_exists()
    {
        $this->assertNull(
            FjordPage::current()
        );
    }

    /** @test */
    public function test_current_method()
    {
        $route = m::mock('route');
        $route->shouldReceive('getName')->andReturn('home');

        Request::setRouteResolver(fn () => $route);

        FjordPage::current();
    }
}
