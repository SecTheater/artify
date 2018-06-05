<?php

namespace Artify\Artify\Artifies;

use File;
use Illuminate\Console\Command;

class ResponseMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'artify:response {name : The name of the response to be created}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a responsable class for your actions';

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
        $model = strpos($name, 'Response') ? explode('Response', $name)[0] : $name;

        if (!is_dir(app_path('/Responses'))) {
            File::makeDirectory(app_path('/Responses'));
        }

        if (file_exists(app_path('/Responses/'.$name.'.php'))) {
            return $this->error('Response already exists');
        }

        $defaultResponseContent = File::get(artify_path('artifies/stubs/DummyResponse.stub'));
        $runtimeResponseContent = str_replace('Dummy', $model, $defaultResponseContent);
        File::put(artify_path('artifies/stubs/DummyResponse.stub'), $runtimeResponseContent);
        File::copy(artify_path('artifies/stubs/DummyResponse.stub'), app_path('/Responses/'.$name.'.php'));
        File::put(artify_path('artifies/stubs/DummyResponse.stub'), $defaultResponseContent);

        $this->info('Well done! response created successfully');
    }
}
