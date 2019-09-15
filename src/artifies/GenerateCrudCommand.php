<?php
namespace Artify\Artify\Artifies;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Str;

class GenerateCrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'artify:crud
                            {model : The name of your model}
                            {--r|repository : Use repository instead of using models}
    ';
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
        if ($this->filesystem->exists(app_path("/Http/Controllers/{$model}Controller.php"))) {
            return $this->error("Controller already exists");
        }

        if (!$this->filesystem->exists(app_path($model . '.php'))) {
            $this->call('make:model', ['name' => $model]);
        }
        if (!$this->filesystem->exists(app_path('/Http/Requests/' . $model . 'StoreRequestForm.php'))) {
            $this->call('make:request', ['name' => "{$model}StoreRequestForm"]);
        }
        if (!$this->filesystem->exists(app_path('/Http/Requests/' . $model . 'UpdateRequestForm.php'))) {
            $this->call('make:request', ['name' => "{$model}UpdateRequestForm"]);
        }
        $defaultControllerContent = $this->filesystem->get(artify_path('artifies/stubs/DummyController.stub'));
        if ($this->option('repository')) {
            $this->call('artify:repository', ['name' => "{$model}Repository"]);
            $runtimeControllerContent = str_replace(['$dummy->delete();', '$dummy->update(request()->all());', 'Dummy::latest()', 'Dummy::create(request()->all());'], ['\DummyRepository::delete($dummy->id);', '\DummyRepository::update($dummy->id, request()->all());', '\DummyRepository::latest()', '\DummyRepository::create(request()->all());'], $defaultControllerContent);
        }
        if (!$this->filesystem->exists(app_path('/Http/Controllers/' . $model . 'Controller.php'))) {
            if (!config('artify.cache.enabled')) {
                $layerName = strpos($runtimeControllerContent ?? $defaultControllerContent, '\DummyRepository') ? '\DummyRepository' : 'Dummy';
                if ($this->option('repository')) {
                    $assignedLayer = '$dummies = \\' . $model . 'Repostiroy::latest()->get();';
                }
                $runtimeControllerContent = str_replace(["cache()->forget('dummies');\n", "cache('dummies')", 'cache()->remember(\'dummies\', config(\'artify.cache.duration\'), function () {
            $dummies = ' . $layerName . '::latest()->get();
        });', ], ['', '$dummies', $assignedLayer ?? '$dummies = Dummy::latest()->get();'], $runtimeControllerContent ?? $defaultControllerContent);
            }
            $runtimeControllerContent = str_replace(
                ['dummy', 'Dummy', 'dummies'],
                [strtolower($model), ucfirst($model), strtolower(Str::plural($model))],
                $runtimeControllerContent ?? $defaultControllerContent
            );
            $this->filesystem->put(artify_path('artifies/stubs/DummyController.stub'), $runtimeControllerContent);
            $this->filesystem->copy(artify_path('artifies/stubs/DummyController.stub'), app_path('/Http/Controllers/' . $model . 'Controller.php'));
            $this->filesystem->put(artify_path('artifies/stubs/DummyController.stub'), $defaultControllerContent);
            $this->filesystem->append(base_path('routes/web.php'), "\nRoute::resource('" . strtolower($model) . "', '{$model}Controller');");
            $this->info("{$model} crud created successfully");
        }
    }
}
