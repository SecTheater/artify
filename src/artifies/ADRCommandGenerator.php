<?php
namespace Artify\Artify\Artifies;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ADRCommandGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'adr:generate {domain : Name Of The Domain Folder.} {name? : name of  the file} {--A|all} {--m|model : model associated with repository} {--s|service} {--c|collection} {--r|responder} {--a|action}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $files = [];
    protected $requiresDomain = ['Model.stub', 'Service.stub', 'Resource.stub'];
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->domain = ucfirst($this->argument('domain'));
        $this->name = ucfirst($this->argument('name'));
        $this->setFiles();
        foreach ($this->files as $filename) {
            if (in_array($filename, $this->requiresDomain)) {
                $customName = str_singular($this->domain);
                $this->hasOrCreateDirectory($this->domain . '/Domain/' . str_plural(explode('.', $filename)[0]));
            } else {
                $this->hasOrCreateDirectory($this->domain . '/' . str_plural(explode('.', $filename)[0]));
                $customName = null;
            }
            if ($originalContent = $this->hasOrFetchDummyFile($filename)) {
                if ($filename == 'Model.stub') {
                    $this->call('make:model', [
                        'name' => $this->domain . '\\Domain\\Models\\' . $customName,
                        '--migration' => true,
                        '--factory' => true,
                    ]);
                    $this->call('make:observer', [
                        'name' => 'App\\' . $this->domain . '\\Domain\\Observers\\' . $customName . 'Observer',
                        '--model' => $this->domain . '\\Domain\\Models\\' . $customName,
                    ]);
                    $this->call('artify:repository', [
                        'name' => 'App\\' . $this->domain . '\\Domain\\Repositories\\' . $customName . 'Repository',
                    ]);
                } else {
                    if ($filename == 'Service.stub') {
                        $fileContent = str_replace(['DummyDomain', 'DummyName', 'Dummy', 'dummies', 'dummy'], [$this->domain, $this->name, str_singular($this->domain), strtolower($this->domain), strtolower(str_singular($this->domain))], $originalContent);
                    } else {
                        $fileContent = str_replace(['DummyDomain', 'Dummy', 'dummies', 'dummy'], [$this->domain, $customName ?? $this->name, strtolower(str_plural($customName ?? $this->name)), strtolower($customName ?? $this->name)], $originalContent);
                    }
                    $this->filesystem->put(__DIR__ . '/stubs/adr/' . $filename, $fileContent);
                    $this->filesystem->copy(__DIR__ . '/stubs/adr/' . $filename, $this->getMovingFilePath($filename));
                    $this->filesystem->put(__DIR__ . '/stubs/adr/' . $filename, $originalContent);
                }
            }
        }
    }
    protected function getMovingFilePath($filename)
    {
        $stubName = explode('.', $filename);
        if (in_array($filename, $this->requiresDomain)) {
            if ($filename == 'Service.stub') {
                $customName = $this->name . $stubName[0];
            } else {
                $customName = str_singular($this->domain);
                if ($filename != 'Model.stub') {
                    $customName .= $stubName[0];
                }
            }
            return (app_path($this->domain . '/Domain/' . str_plural($stubName[0]) . '/' . $customName . '.php'));
        }
        $customName = $this->name . $stubName[0];
        return app_path($this->domain . '/' . str_plural($stubName[0]) . '/' . $customName . '.php');
    }
    protected function hasOrFetchDummyFile($file)
    {
        if ($this->filesystem->isFile($this->getMovingFilePath($file))) {
            return false;
        }
        return $this->filesystem->get(__DIR__ . '/stubs/adr/' . $file);
    }
    protected function hasOrCreateDirectory($domain)
    {
        if (!$this->filesystem->isDirectory(app_path($domain))) {
            $this->filesystem->makeDirectory(app_path($domain), 0755, true, true);
        }
    }
    protected function setFiles()
    {
        if ($this->option('all')) {
            $this->files = ['Action.stub',
                'Model.stub',
                'Responder.stub',
                'Service.stub',
                'Resource.stub',
            ];
            return;
        }
        $files = [];
        if ($this->option('model')) {
            $files = array_merge($files, [
                'Model.stub',
            ]);
        }
        if ($this->option('service')) {
            $files = array_merge($files, [
                'Service.stub',
            ]);
        }
        if ($this->option('collection')) {
            $files = array_merge($files, [
                'Resource.stub',
            ]);
        }
        if ($this->option('responder')) {
            $files = array_merge($files, [
                'Responder.stub',
            ]);
        }
        if ($this->option('action')) {
            $files = array_merge($files, [
                'Action.stub',
            ]);
        }
        $this->files = $files;
    }
}
