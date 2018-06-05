<?php

namespace Artify\Artify\Artifies;

use File;
use Illuminate\Console\Command;

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
                            {--f|facade=true : Whether to create facade or not}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a repository for your application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $model = strpos($name, 'Repository') ? explode('Repository', $name)[0] : $name;

        if (!is_dir(app_path('/Repositories'))) {
            File::makeDirectory(app_path('/Repositories'));
        }

        if (!is_dir(app_path('/Repositories/Contracts'))) {
            File::makeDirectory(app_path('/Repositories/Contracts'));
        }

        if (!File::exists(app_path('/Repositories/Contracts/RepositoryInterface.stub'))) {
            File::copy(artify_path('artifies/stubs/RepositoryInterface.stub'), app_path('/Repositories/Contracts/RepositoryInterface.php'));
        }

        if (!File::exists(app_path('/Repositories/Repository.php'))) {
            File::copy(artify_path('artifies/stubs/Repository.stub'), app_path('/Repositories/Repository.php'));
        }

        if (File::exists(app_path('/Repositories/'.$name.'.php'))) {
            return $this->error('Repository already exists');
        }

        if ($this->option('model')) {
            $this->call('make:model', ['name' => $model]);
        }

        if ($this->option('facade')) {
            $this->call('artify:facade', ['name' => $name]);
        }

        $defaultRepositoryContent = File::get(artify_path('artifies/stubs/DummyRepository.stub'));
        $runtimeRepositoryContent = str_replace(['Dummy'], [ucfirst($model)], $defaultRepositoryContent);
        File::put(artify_path('artifies/stubs/DummyRepository.stub'), $runtimeRepositoryContent);
        File::copy(artify_path('artifies/stubs/DummyRepository.stub'), app_path('/Repositories/'.$name.'.php'));
        File::put(artify_path('artifies/stubs/DummyRepository.stub'), $defaultRepositoryContent);

        $this->info('Yeey! Repository created successfully');
    }
}
