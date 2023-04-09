<?php

namespace App\Console\Commands\Steam;

use App\Traits\Env\SteamUsersTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class GetUsers extends Command
{
    use SteamUsersTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:get-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List of users from env file';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $defaultUser = $this->getDefaultUser();

        $users = Arr::map($this->getUsers(), function ($userAlias) use ($defaultUser) {
            return [$userAlias, $userAlias === $defaultUser];
        });

        $this->table(['User Alias', 'Default'], $users);
    }
}
