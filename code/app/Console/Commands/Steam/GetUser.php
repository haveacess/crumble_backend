<?php

namespace App\Console\Commands\Steam;

use App\Services\Steam\AuthService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class GetUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:get-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get base info about authenticated steam user';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Cookie container locate here: ' . AuthService::COOKIE_PATH);
        $this->info('If you wanna to change Steam profile, please edit this file');

        $authService = new AuthService();
        $userInfo = (array)$authService->getUserInfo();

        $this->table(
            array_keys($userInfo), [array_values($userInfo)]
        );
    }
}
