<?php

namespace Artify\Artify\Artifies;

class ResponseMakeCommand extends GeneratorCommand
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
    protected $type = 'Response';

    protected function getStub()
    {
        return artify_path('artifies/stubs/DummyResponse.stub');
    }
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Responses';
    }


}
