<?php

namespace App\Console\Commands\Steam;

use App\Exceptions\UnauthorizedUserException;
use App\Services\Steam\UserService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;

class GetUserWallet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:get-user-wallet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get balance and other wallet info';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userService = new UserService('vladya_merchant');
        try {
            $wallet = $userService->getWallet();
            $this->info("Balance: {$wallet->getBalance()} {$wallet->getCurrency()}");

            return self::SUCCESS;
        } catch (UnauthorizedUserException $e) {
            $this->error('User: ' . $userService->getProfileAlias() . ' is unauthorized');
            return self::FAILURE;
        } catch (GuzzleException $e) {
            $this->error('Some error with request');
            return self::FAILURE;
        }
    }
}
