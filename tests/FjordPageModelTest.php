<?php

namespace Tests;

use Fjord\Support\Facades\Config;
use FjordPages\FjordPagesCollection;
use FjordPages\Models\FjordPage;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
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
    public function test_current_method_returns_current_route()
    {
        $route = m::mock('route');
        $route->shouldReceive('getName')->andReturn('home');

        Request::setRouteResolver(fn () => $route);

        FjordPage::current();
    }

    /** @test */
    public function it_has_required_fillable_attributes()
    {
        $fillable = (new FjordPage)->getFillable();
        $this->assertContains('title', $fillable);
        $this->assertContains('value', $fillable);
        $this->assertContains('collection', $fillable);
        $this->assertContains('config_type', $fillable);
    }

    /** @test */
    public function test_isTranslatable_method_is_false_by_default()
    {
        $page = new FjordPage();
        $this->assertFalse($page->isTranslatable());
    }

    /** @test */
    public function test_isTranslatable_method_returns_config_translatable_value()
    {
        $config = m::mock('config');
        $config->translatable = 'foo';
        Config::partialMock()->shouldReceive('get')->andReturn($config);
        $page = new FjordPage();
        $page->config_type = static::class;
        $this->assertSame('foo', $page->isTranslatable());
    }

    /** @test */
    public function test_non_translatable_title_attribute()
    {
        $page = new FjordPage(['title' => 'foo']);
        $this->assertSame('foo', $page->title);
    }

    /** @test */
    public function test_translatable_title_attribute()
    {
        $this->app->setLocale('en');
        $config = m::mock('config');
        $config->translatable = true;
        Config::partialMock()->shouldReceive('get')->andReturn($config);
        $page = new FjordPage(['title' => 'foo', 'en' => ['t_title' => 'bar']]);
        $page->config_type = static::class;
        $this->assertSame('bar', $page->title);
    }

    /** @test */
    public function test_non_translatable_slug_attribute()
    {
        $page = new FjordPage();
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
        $page = new FjordPage(['en' => []]);
        $page->slug = 'foo';
        $page->t_slug = 'bar';
        $page->config_type = static::class;
        $this->assertSame('bar', $page->slug);
    }

    /** @test */
    public function test_unique_slug_constraints()
    {
        $this->artisan('migrate:fresh');
        $page1 = FjordPage::create(['title' => 'title', 'collection' => '', 'config_type' => 'foo']);
        $page2 = FjordPage::create(['title' => 'title', 'collection' => '', 'config_type' => 'foo']);
        $page3 = FjordPage::create(['title' => 'title', 'collection' => '', 'config_type' => 'bar']);
        $this->assertNotEquals($page1->slug, $page2->slug);
        $this->assertEquals($page1->slug, $page3->slug);
    }

    /** @test */
    public function test_collection_is_FjordPagesCollection_instance()
    {
        $this->artisan('migrate:fresh');
        $this->assertInstanceOf(FjordPagesCollection::class, FjordPage::all());
    }

    /** @test */
    public function test_content_relation_method()
    {
        $this->assertInstanceOf(Relation::class, (new FjordPage)->content());
    }

    /** @test */
    public function test_getRouteName_method()
    {
        $this->assertSame('pages.foo', (new FjordPage(['collection' => 'foo']))->getRouteName());
    }

    /** @test */
    public function test_getRouteName_method_for_translatable_pages()
    {
        $config = m::mock('config');
        $config->translatable = true;
        Config::partialMock()->shouldReceive('get')->andReturn($config);
        $page = new FjordPage(['collection' => 'foo']);
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
        $page = FjordPage::create(['title' => 'bar', 'collection' => '', 'config_type' => '']);
        $this->assertSame('foo', $page->getRoute());
    }

    /** @test */
    public function test_uri_attribute()
    {
        $this->artisan('migrate:fresh');
        URL::partialMock()->shouldReceive('route')->andReturn('foo');
        $page = FjordPage::create(['title' => 'bar', 'collection' => '', 'config_type' => '']);
        $this->assertSame('foo', $page->uri);
    }
}
