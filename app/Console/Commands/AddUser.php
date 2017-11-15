<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\GetUserData;

class AddUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:user {id} {token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add user to db';

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
        dispatch((new GetUserData($this->argument('id'), $this->argument('token'))));
    }
}
