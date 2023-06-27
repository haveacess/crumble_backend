<?php

namespace App\Console\Commands\Steam;

use App\Exceptions\NotFoundEntityException;
use App\Models\ItemModel;
use App\Services\Steam\Marketplace\PriceHistoryService;
use App\Traits\Env\SteamUsersTrait;
use Illuminate\Console\Command;

class UpdatePriceHistory extends Command
{
    use SteamUsersTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:update-price-history
                            {marketHashName : market_hash_name item. Example: SCAR-20 | Cardiac (Field-Tested)}
                            {--user= : User alias for receive history. Leave blank for set default_user}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updating price history for specific item';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userAlias = $this->option('user') ?? $this->getDefaultUser();
        $marketHashName = $this->argument('marketHashName');

        try {
            $itemModel = ItemModel::where('market_hash_name', $marketHashName)->limit(1)
                ->firstOr(function () use ($marketHashName) {
                    throw new NotFoundEntityException($marketHashName, ItemModel::class);
                });

            $service = new PriceHistoryService($userAlias);

            $history = $service->fetchItemPrices($itemModel);

            return self::SUCCESS;
        } catch (NotFoundEntityException) {
            $this->error("${marketHashName} not found in database");
            return self::FAILURE;
        }
    }
}
