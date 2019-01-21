<?php

namespace Artify\Artify\Artifies;

use Artify\Artify\ArtifyServiceProvider;
use Artify\Artify\Contracts\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallCommand extends Command {
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
	public function __construct(Filesystem $filesystem) {
		parent::__construct();
		$this->filesystem = $filesystem;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$os = (substr(php_uname('a'), 0, 3));
		$name = (strtoupper($os) !== 'WIN') ? posix_getpwuid(posix_geteuid())['name'] : 'windows user';
		$time = (\Carbon\Carbon::now()->format('A') === 'AM') ? 'Morning' : 'Evening';
		$this->info("Good $time $name , Let's set a couple of things.");
		$this->call('vendor:publish', ['--provider' => ArtifyServiceProvider::class]);
		$this->info('Tip : You can modify the config/artify.php manually');
		$this->info('the default column in charge of permissions within your role model is named permissions, change it or leave blank.');
		$structure = $this->choice('Ehum Ehum, what sturcture do you follow ?', ['ADR', 'MVC'], 'MVC');
		if ($structure === 'ADR') {
			config(['artify.adr.enabled' => true]);
			config(['artify.adr.domains' => []]);

			$this->call('adr:install');
		} else {
			config(['artify.adr.enabled' => false]);
		}
		config(['artify.permissions_column' => 'permissions']);
		if ($this->confirm('Do you use multi tenancy for this project ?')) {
			config(['artify.is_multi_tenancy' => true]);
			$tenant = $this->ask('Enter the class (namespaced) of the file which in charge of the tenancy.');
			config()->set('artify.tenant', $tenant);
			app()->bind(Tenant::class, $tenant);
			$this->filesystem->makeDirectory(database_path('migrations/tenant'), 0755, true, true);
		}

		if ($this->argument('duration') && $this->option('cache')) {
			config(['artify.cache.enabled' => true, 'artify.cache.duration' => (int) $this->argument('duration')]);
		} else {
			config(['artify.cache.enabled' => false, 'artify.cache.duration' => 10]);
		}

		$configRunTimeContent = var_export(config('artify'), true);
		$this->filesystem->put(config_path('artify.php'), '');
		$configRunTimeContent = str_replace(['array (', ')'], ['[', ']'], $configRunTimeContent);
		$this->filesystem->put(config_path('artify.php'), "<?php\n\n return " . $configRunTimeContent . ';');
		$this->info('Everything is set, Enjoy Being an Artifier');
	}
}
