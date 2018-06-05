<?php

namespace Artify\Artify\Artifies;

use Artify\Artify\ArtifyServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'artify:install {--c|cache : enable cache} {duration? : specify the duration in minutes.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register The Artify Commands.';
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
        $os = (substr(php_uname('a'), 0, 3));
        $name = (strtoupper($os) !== 'WIN') ? posix_getpwuid(posix_geteuid())['name'] : null;
        $time = (\Carbon\Carbon::now()->format('A') === 'AM') ? 'Morning' : 'Evening';
        $this->info("Good $time $name , Let's set a couple of things.");
        $this->call('vendor:publish', ['--provider' => ArtifyServiceProvider::class]);
        $this->info('Tip : You can modify the config/artify.php manually');
        $roleModel = $this->ask('Enter The Name of Your Role Model, If you don\'t have one, we will create it.');
        $this->info('the default column in charge of permissions within your role model is named permissions, change it or leave blank.');
        $permissionsColumn = $this->ask('Enter The Name of Permissions Column within your Role Model') ?? 'permissions';
        if ($roleModel && !$this->filesystem->exists(app_path($roleModel.'.php'))) {
            $this->call('make:model', ['name' => ucfirst($roleModel)]);
        }

        $this->call('config:clear');
        config(['artify.models.role' => ucfirst($roleModel)]);
        config(['artify.permissions_column' => $permissionsColumn]);

        if ($this->argument('duration') && $this->option('cache')) {
            config(['artify.cache.enabled' => true, 'artify.cache.duration' => (int) $this->argument('duration')]);
        } else {
            config(['artify.cache.enabled' => false]);
        }
        $configRunTimeContent = var_export(config('artify'), true);
        $this->filesystem->put(config_path('artify.php'), '');
        $configRunTimeContent = str_replace(['array (', ')'], ['[', ']'], $configRunTimeContent);
        $this->filesystem->put(config_path('artify.php'), "<?php\n\n return ".$configRunTimeContent.';');
        $this->info('Everything is set, Enjoy Being an Artifier');
    }
}
