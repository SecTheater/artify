<?php

namespace Artify\Artify\Artifies;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ObserverMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'artify:observer {name : name of the observer.} {--class= : name of the model} {except?* : generate all Eloquent events except for specific ones.}';

    protected $events = [
        'retrieved', 'creating', 'created', 'updating', 'updated',
        'saving', 'saved', 'restoring', 'restored',
        'deleting', 'deleted', 'forceDeleted',
    ];
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new observer class';

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
        if (!$this->filesystem->isDirectory(app_path('Observers'))) {
            $this->filesystem->makeDirectory(app_path('Observers'), 0755, false, true);
            $this->info('Observers folder generated !');
        }
        if (!$this->filesystem->exists(app_path('Observers'.DIRECTORY_SEPARATOR.$this->argument('name').'Observer.php'))) {
            $hasModel = trim($this->option('class')) != '' ? trim($this->option('class')) : null;
            if ($hasModel) {
                $model = '\\App\\'.ucfirst($hasModel);
                $model = new $model();
                $model = $model->getObservableEvents();
                $loweredCaseModel = lcfirst($this->option('class'));
            } else {
                $model = $this->events;
                $loweredCaseModel = null;
            }
            $fileContent = '';
            $if = function ($condition, $applied, $rejected) {
                return $condition ? $applied : $rejected;
            };
            foreach ($model as $event) {
                if (in_array($event, $this->argument('except'))) {
                    continue;
                }
                $fileContent .= <<<Event
    public function {$event}({$if($hasModel, "$hasModel", '')} {$if($hasModel && isset($loweredCaseModel), "\$$loweredCaseModel" ?? null, '')}){
                    
    }

Event;
            }
            $fileContent = <<<content
<?php
namespace \App\Observers;

{$if(isset($hasModel) && !empty($hasModel), "use \App\\$hasModel;", '')}

class {$this->argument('name')}Observer {
    {$fileContent}
}
?>
content;
            $this->filesystem->put(app_path('Observers'.DIRECTORY_SEPARATOR.$this->argument('name').'Observer.php'), $fileContent);
            $this->info('Observer Created !');
        } else {
            $this->info('Observer Already Exists ! , Wake up you need some caffeine :)');
        }
    }
}
