<?php

namespace Artify\Artify\Artifies;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class GenerateCrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'artify:crud {model : The name of your model} {--r|repository : Use repository instead of using models}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates The Model,Resource Controller , RequestForm & Route.';

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
        $model = $this->argument('model');

        if (!$this->filesystem->exists(app_path($model.'.php'))) {
            $this->call('make:model', ['name' => $model]);
        }

        if (!$this->filesystem->exists(app_path('/Http/Requests/'.$model.'StoreRequestForm.php'))) {
            $this->call('make:request', ['name' => "{$model}StoreRequestForm"]);
        }

        if (!$this->filesystem->exists(app_path('/Http/Requests/'.$model.'UpdateRequestForm.php'))) {
            $this->call('make:request', ['name' => "{$model}UpdateRequestForm"]);
        }

        if ($this->option('repository')) {
            $this->call('artify:repository', ['name' => "{$model}Repository"]);
        }

        if (!$this->filesystem->exists(app_path('/Http/Controllers/'.$model.'Controller.php'))) {
            $defaultControllerContent = $this->filesystem->get(artify_path('artifies/stubs/DummyController.stub'));

            $runtimeControllerContent = str_replace(
                ['dummy', 'Dummy', 'dummies'],
                [strtolower($model), ucfirst($model), strtolower(str_plural($model))],
                $defaultControllerContent
            );

            $this->filesystem->put(artify_path('artifies/stubs/DummyController.stub'), $runtimeControllerContent);
            $this->filesystem->copy(artify_path('artifies/stubs/DummyController.stub'), app_path('/Http/Controllers/'.$model.'Controller.php'));
            $this->filesystem->put(artify_path('artifies/stubs/DummyController.stub'), $defaultControllerContent);
            $this->filesystem->append(base_path('routes/web.php'), "\nRoute::resource('$model','{$model}Controller');\n");
            $this->info("{$model} crud created successfully");
        }
    }
}
