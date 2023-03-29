<?php

namespace App\Console\Commands\Steam;

use App\Exceptions\UnauthorizedUserException;
use App\Services\Steam\UserService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;

class GetUserInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:get-user-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get base info about authenticated steam user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userService = new UserService('vladya_merchant');

        try {
            $userInfo = $userService->getBaseInfo(false)->toArray();

            $this->table(
                array_keys($userInfo), [array_values($userInfo)]
            );

            return self::SUCCESS;
        } catch (UnauthorizedUserException) {
            $this->error('User: ' . $userService->getProfileAlias() . ' is unauthorized');

            $this->info('Cookie container locate here: ' . $userService->getCookieContainerPath());
            $this->info('Put cookie\'s in this file if you wanna to login by this Steam profile');
            return self::FAILURE;
        } catch (GuzzleException) {
            $this->error('Some error with request');
            return self::FAILURE;
        }
    }
}
