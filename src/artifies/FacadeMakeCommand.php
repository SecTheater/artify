<?php
namespace Artify\Artify\Artifies;

use Illuminate\Console\Command;

class FacadeMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'artify:facade {name : The name of the facade to be created}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a facade for your application';
    /**
     * The console command type.
     *
     * @var string
     */
    protected $type = 'Facade';
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    protected function getStub()
    {
        return base_path('vendor/laravel/framework/src/illuminate/foundation/stubs/facade.stub');
    }
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Facades';
    }
    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            ['DummyNamespace'],
            [$this->getNamespace($name)],
            $stub
        );
        return $this;
    }
    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);
        return str_replace('DummyTarget', $class, parent::replaceClass($stub, $name));
    }
}
