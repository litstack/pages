# Fjord Pages

Fjord pages help you to quickly add new pages to your fjord application. This function turns your Fjord admin panel into a cms.

Install the package via composer:

```shell
composer require aw-studio/fjord-pages
```

## Setup a pages collection

With the artisan command fjord:pages a new pages collection is created. For example `blog`:

```shell
php artisan fjord:pages Blog
```

A config is created and two controllers, one for the fjord backend in `./fjord/app/Controllers/Pages` and one for your application in `./app/Http/Controllers/Pages`.

In the config you can configure the route prefix and the possible repeatabels. The url of the page consists of the route prefix specified in the config and the sluggified page title. So a route for the following case could be `/blog/my-title`. If the page is translatable a route is created for each locale specified in the config like so:

-   `en/blog/{slug}`
-   `en/blog/{slug}`

```php
// ./fjord/app/Config/Pages/BlogConfig.php

namespace FjordApp\Config\Pages;

...

class BlogConfig extends PagesConfig
{
    ...

    public function appRoutePrefix(string $locale = null)
    {
        return "blog";
    }

    public function repeatables(Repeatables $rep)
    {
        // Build your repeatables in here.
    }
}
```

In the controller the page model is loaded with the method `getFjordPage`. This can now be passed to a view like this:

```php

namespace App\Http\Controllers\Pages;

use FjordPages\ManagesFjordPages;
use Illuminate\Http\Request;

class PagesController
{
    use ManagesFjordPages;

    public function __invoke(Request $request, $slug)
    {
        $page = $this->getFjordPage($slug);

        return view('pages.blog')->withPage($page);
    }
}
```
