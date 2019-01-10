<?php

namespace Artify\Artify\Artifies;

use Artify\Artify\Models\Role;
use Artify\Artify\Models\User;
use Illuminate\Console\Command;

class UserAssignCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'artify:assign
                {email : The email of the user}
                {rank : The rank of the user (slug within the roles table.)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup a fresh privilege to a user ( deletes all of old permissions and assign new one )';

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
        $user = User::where('email',$this->argument('email'))->firstOrFail();

        $role = Role::whereSlug($this->argument('rank'))->firstOrFail();
        $user->update([config('artify.permissions_column') => null]);
        $user->roles()->sync($role);
        return $this->info("$user->first_name  Role is set to  `{$role->slug}`");
    }
}
