<?php

namespace Tests;

use Ignite\Support\Facades\Config;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Litstack\Pages\Models\Page;
use Litstack\Pages\PagesCollection;
use Mockery as m;

class PageModelTest extends TestCase
{
    /** @test */
    public function test_current_method_doesnt_fail_when_no_route_exists()
    {
        $this->assertNull(
            Page::current()
        );
    }

    /** @test */
    public function test_current_method_returns_current_route()
    {
        $route = m::mock('route');
        $route->shouldReceive('getName')->andReturn('home');

        Request::setRouteResolver(fn () => $route);

        Page::current();
    }

    /** @test */
    public function it_has_required_fillable_attributes()
    {
        $fillable = (new Page)->getFillable();
        $this->assertContains('title', $fillable);
        $this->assertContains('value', $fillable);
        $this->assertContains('collection', $fillable);
        $this->assertContains('config_type', $fillable);
    }

    /** @test */
    public function test_isTranslatable_method_is_false_by_default()
    {
        $page = new Page();
        $this->assertFalse($page->isTranslatable());
    }

    /** @test */
    public function test_isTranslatable_method_returns_config_translatable_value()
    {
        $config = m::mock('config');
        $config->translatable = 'foo';
        Config::partialMock()->shouldReceive('get')->andReturn($config);
        $page = new Page();
        $page->config_type = static::class;
        $this->assertSame('foo', $page->isTranslatable());
    }

    /** @test */
    public function test_non_translatable_title_attribute()
    {
        $page = new Page(['title' => 'foo']);
        $this->assertSame('foo', $page->title);
    }

    /** @test */
    public function test_translatable_title_attribute()
    {
        $this->app->setLocale('en');
        $config = m::mock('config');
        $config->translatable = true;
        Config::partialMock()->shouldReceive('get')->andReturn($config);
        $page = new Page(['title' => 'foo', 'en' => ['t_title' => 'bar']]);
        $page->config_type = static::class;
        $this->assertSame('bar', $page->title);
    }

    /** @test */
    public function test_non_translatable_slug_attribute()
    {
        $page = new Page();
        $page->slug = 'foo';
        $this->assertSame('foo', $page->slug);
    }

    /** @test */
    public function test_translatable_slug_attribute()
    {
        $this->app->setLocale('en');
        $config = m::mock('config');
        $config->translatable = true;
        Config::partialMock()->shouldReceive('get')->andReturn($config);
        $page = new Page(['en' => []]);
        $page->slug = 'foo';
        $page->t_slug = 'bar';
        $page->config_type = static::class;
        $this->assertSame('bar', $page->slug);
    }

    /** @test */
    public function test_unique_slug_constraints()
    {
        $this->artisan('migrate:fresh');
        $page1 = Page::create(['title' => 'title', 'collection' => '', 'config_type' => 'foo']);
        $page2 = Page::create(['title' => 'title', 'collection' => '', 'config_type' => 'foo']);
        $page3 = Page::create(['title' => 'title', 'collection' => '', 'config_type' => 'bar']);
        $this->assertNotEquals($page1->slug, $page2->slug);
        $this->assertEquals($page1->slug, $page3->slug);
    }

    /** @test */
    public function test_collection_is_FjordPagesCollection_instance()
    {
        $this->artisan('migrate:fresh');
        $this->assertInstanceOf(PagesCollection::class, Page::all());
    }

    /** @test */
    public function test_content_relation_method()
    {
        $this->assertInstanceOf(Relation::class, (new Page)->content());
    }

    /** @test */
    public function test_getRouteName_method()
    {
        $this->assertSame('pages.foo', (new Page(['collection' => 'foo']))->getRouteName());
    }

    /** @test */
    public function test_getRouteName_method_for_translatable_pages()
    {
        $config = m::mock('config');
        $config->translatable = true;
        Config::partialMock()->shouldReceive('get')->andReturn($config);
        $page = new Page(['collection' => 'foo']);
        $page->config_type = static::class;

        $this->app->setLocale('en');
        $this->assertSame('en.pages.foo', $page->getRouteName());
        $this->assertSame('en.pages.foo', $page->getRouteName('en'));
        $this->assertSame('de.pages.foo', $page->getRouteName('de'));
    }

    /** @test */
    public function test_getRoute_attribute()
    {
        $this->artisan('migrate:fresh');
        URL::partialMock()->shouldReceive('route')->andReturn('foo');
        $page = Page::create(['title' => 'bar', 'collection' => '', 'config_type' => '']);
        $this->assertSame('foo', $page->getRoute());
    }

    /** @test */
    public function test_uri_attribute()
    {
        $this->artisan('migrate:fresh');
        URL::partialMock()->shouldReceive('route')->andReturn('foo');
        $page = Page::create(['title' => 'bar', 'collection' => '', 'config_type' => '']);
        $this->assertSame('foo', $page->uri);
    }
}
