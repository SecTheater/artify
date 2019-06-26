<?php
namespace Artify\Artify\Artifies;

use Illuminate\Console\GeneratorCommand as BaseGeneratorCommand;
use Illuminate\Filesystem\Filesystem;

abstract class GeneratorCommand extends BaseGeneratorCommand
{
    const MVC_MODE = 0;
    const ADR_MODE = 1;
    public function __construct(Filesystem $files)
    {
        parent::__construct($files);
    }
    protected function getArchitectureMode()
    {
        return config('artify.is_adr') ? self::ADR_MODE : self::MVC_MODE;
    }
    protected function getApplicationConfig()
    {
        return $this->files->get(config_path('app.php'));
    }
    protected function getClassName($name)
    {
        return str_replace($this->getNamespace($name) . '\\', '', $name);
    }
}
