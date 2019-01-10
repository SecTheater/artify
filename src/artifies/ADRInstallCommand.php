<?php
namespace Artify\Artify\Artifies;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
class ADRInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'adr:install';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $from_directory = null;
    protected $to_directory = null;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $filesystem) {
        parent::__construct();
        $this->filesystem = $filesystem;
        $this->from_directory = artify_path('artifies/stubs/adr/App');
        $this->to_directory = app_path('App');
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->hasOrCreateDirectory('App');
        $this->recursive_copy($this->from_directory , $this->to_directory);
    }
    public function recursive_copy($src,$dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    $this->recursive_copy($src .'/'. $file, $dst .'/'. $file);
                }
                else {
                    copy($src .'/'. $file,$dst .'/'. str_replace('stub', 'php', $file));
                }
            }
        }
        closedir($dir);
    }
    protected function hasOrCreateDirectory($domain) {
        if (!$this->filesystem->isDirectory(app_path($domain))) {
            $this->filesystem->makeDirectory(app_path($domain), 0755, true, true);
        }
    }
}