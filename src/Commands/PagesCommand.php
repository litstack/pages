<?php

namespace Litstack\Pages\Commands;

use Fjord\Commands\GeneratorCommand;
use Illuminate\Support\Str;

class PagesCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fjord:pages {collection?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create controller and config file for a new pages collection.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Pages config';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setCollectionName();

        parent::handle();

        $this->makeController();
    }

    /**
     * Make form controller.
     *
     * @return void
     */
    protected function makeController()
    {
        $this->call('fjord:pages-controller', [
            'name' => $this->getControllerClass(),
        ]);

        $this->call('fjord:pages-controller', [
            'name'  => $this->getControllerClass(),
            '--app' => true,
        ]);
    }

    /**
     * Get stub path.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__.'/../../stubs/page.config.stub';
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return $this->collectionClass;
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param  string $name
     * @return string
     */
    protected function buildClass($name)
    {
        $replace = [
            'DummyCollectionRouteName' => Str::slug($this->collectionName),
            'DummyController'          => $this->getControllerClass(),
            'DummyPageName'            => Str::singular($this->collectionClass),
            'DummyPagesName'           => Str::plural($this->collectionClass),
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        );
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\Config\\Pages';
    }

    /**
     * Get controller class.
     *
     * @return string
     */
    protected function getControllerClass()
    {
        return $this->collectionClass.'Controller';
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        if (! Str::endsWith($name, 'Config')) {
            $name .= 'Config';
        }

        return parent::qualifyClass($name);
    }

    /**
     * Set collection and form name.
     *
     * @return void
     */
    protected function setCollectionName()
    {
        $collectionName = $this->argument('collection');
        if (! $collectionName) {
            $collectionName = $this->ask('Enter the pages collection name (snake_case)');
        }

        $this->collectionName = Str::snake($collectionName);
        $this->collectionClass = ucfirst(Str::camel($this->collectionName));
    }
}
