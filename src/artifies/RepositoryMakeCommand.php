<?php
namespace Artify\Artify\Artifies;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class RepositoryMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'artify:repository
                            {name : The name for the repository to be created}
                            {--m|model : Whether to create model or not}
    ';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a repository for your application';
    protected $filesystem;
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
        $name = $this->argument('name');
        if (str_contains($name, '\\')) {
            $location = $this->filesystem->transformNamespaceToLocation($name);
            $filename = $this->filesystem->getFileName($name);
        } else {
            $location = 'app/Repositories';
            $filename = $name;
        }
        $model = strpos($filename, 'Repository') ? explode('Repository', $filename)[0] : $filename;
        if (file_exists($location . '/' . $filename . '.php')) {
            return $this->error('Repository already exists');
        }
        $this->filesystem->makeDirectory(base_path($location), 0755, true, true);
        if (config('artify.adr.enabled') && !is_dir(app_path('App/Domain/Contracts'))) {
            $contractLocation = app_path('App/Domain/Contracts/');
            $repositoryLocation = app_path('App/Domain/Repositories/');
            $this->filesystem->makeDirectory($contractLocation, 0755, true, true);
            $this->filesystem->makeDirectory($repositoryLocation, 0755, true, true);
        }
        if (!config('artify.adr.enabled') && !is_dir(app_path('Repositories/Contracts'))) {
            $contractLocation = app_path('Repositories/Contracts/');
            $repositoryLocation = app_path('Repositories/');
            $this->filesystem->makeDirectory($contractLocation, 0755, true, true);
        }
        if (isset($contractLocation) && !$this->filesystem->exists($contractLocation . 'RepositoryInterface.php')) {
            copy(artify_path('artifies/stubs/RepositoryInterface.stub'), $contractLocation . 'RepositoryInterface.php');
        }
        if (isset($repositoryLocation) && !$this->filesystem->exists($repositoryLocation . 'Repository.php')) {
            copy(artify_path('artifies/stubs/Repository.stub'), $repositoryLocation . 'Repository.php');
        }
        $namespacedModel = config('artify.adr.enabled') ? $this->filesystem->getNamespaceFromLocation(substr($location, 0, strrpos($location, '/'))) . '\\Models\\' . $model : config('artify.models.namespace') . $model;
        if ($this->option('model')) {
            if (config('artify.adr.enabled')) {
                $this->call('make:model', [
                    'name' => $namespacedModel,
                ]);
            }
        }
        if (config('artify.adr.enabled')) {
            $namespacedModel = $namespacedModel . ";\nuse App\\Domain\\Repositories\\Repository";
        }
        $defaultRepositoryContent = $this->filesystem->get(artify_path('artifies/stubs/DummyRepository.stub'));
        $runtimeRepositoryContent = str_replace(['DummyNamespace', 'DummyModelNamespace', 'DummyRepository', 'Dummy'], [$this->filesystem->getNamespaceFromLocation($location), $namespacedModel, $filename, ucfirst($model)], $defaultRepositoryContent);
        $this->filesystem->put(artify_path('artifies/stubs/DummyRepository.stub'), $runtimeRepositoryContent);
        $this->filesystem->copy(artify_path('artifies/stubs/DummyRepository.stub'), $location . '/' . $filename . '.php');
        $this->filesystem->put(artify_path('artifies/stubs/DummyRepository.stub'), $defaultRepositoryContent);
        $this->info('Yeey! Repository created successfully');
    }
}
