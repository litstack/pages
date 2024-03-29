<?php

namespace Litstack\Pages\Commands;

use Ignite\Console\GeneratorCommand;
use Illuminate\Support\Str;

class PagesControllerCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lit:pages-controller {name}
                            {--app}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will create a pages controller to the Lit namespace.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Pages controller';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setType();

        return parent::handle();
    }

    /**
     * Set type.
     *
     * @return void
     */
    protected function setType()
    {
        if ($this->option('app')) {
            $this->type == 'App pages controller';

            return;
        }

        $this->type == 'Lit pages controller';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('app')) {
            return __DIR__.'/../../stubs/controller.page.app.stub';
        }

        return __DIR__.'/../../stubs/controller.page.lit.stub';
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
        $replace = $this->buildReplacements($name);

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        );
    }

    /**
     * Build crud controller replacements.
     *
     * @param  string $name
     * @return array
     */
    protected function buildReplacements(string $name)
    {
        $modelClassName = str_replace('Controller', '', last(split_path($name)));
        $tableName = strtolower(Str::plural($modelClassName));

        return [
            'DummyModelClass' => $modelClassName,
            'DummyTableName'  => $tableName,
        ];
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\Http\\Controllers\\Pages';
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        if ($this->option('app')) {
            return 'App';
        }

        return 'Lit';
    }

    public function getPath($name)
    {
        if ($this->option('app')) {
            $name = Str::replaceFirst($this->rootNamespace(), '', $name);

            return app_path(
                str_replace('\\', '/', $name).'.php'
            );
        }

        return parent::getPath($name);
    }
}
