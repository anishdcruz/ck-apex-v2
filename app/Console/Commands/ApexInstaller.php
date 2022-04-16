<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class ApexInstaller extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apex:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install required data.';

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
        $this->call('key:generate');

        $this->call('migrate', ['--force' => true]);

        $this->call('optimize');

        $this->call('route:clear');

        $user = new User([
            'name' => 'Admin',
            'title' => 'System Admin',
            'telephone' => null,
            'extension' => null,
            'mobile_number' => null,
            'email' => 'admin@apex.dev',
            'password' => 'password',
            'email_signature' => 'Best Regards'.PHP_EOL,
        ]);

        $user->is_active = 1;
        $user->is_admin = 1;

        $user->save();

        $this->info('Installation Successful!');
    }
}
