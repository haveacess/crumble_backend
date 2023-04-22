<?php

namespace App\Console\Commands\Steam;

use App\Classes\Filter\Steam\CSGOFilter;
use App\Classes\Pagination;
use App\Exceptions\NotFoundEntityException;
use App\Services\Steam\ItemsService;
use App\Traits\Env\SteamUsersTrait;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Arr;

class UpdateDBItems extends Command
{
    use SteamUsersTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:update-db-items
                            {userAlias?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collecting and update database of items';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $userAlias = $this->argument('userAlias') ?? $this->getDefaultUser();
        $itemsService = new ItemsService($userAlias);

        $filter = CSGOFilter::getMarketplaceItems();
        $pagination = new Pagination(null, 0, 100);
        $pagination->addRateLimiter(5);

        do {
            if (App::hasDebugModeEnabled()) {
                $isConfirmed = $this->confirm('Agree for continue, or abort for break');

                if (!$isConfirmed) {
                    $this->info('Aborted..');
                    break;
                }
            }

            try {
                $this->info(vsprintf('Fetch another items. Offset %s/%s', [
                    $pagination->getOffset(),
                    $pagination->getTotalItemsCount()
                ]));

                $items = $itemsService->fetchItems($filter, $pagination);
                $this->info('Items fetched. Updated..');

                $this->table(['market_hash_name'], Arr::map($items, function ($item) {
                    return ['market_hash_name' => $item->marketHashName];
                }));

                $itemsService->updateItems($items);
            } catch (GuzzleException $e) {
                report($e);
                $this->error('Error fetching another items');
            } catch (QueryException $e) {
                report($e);
                $this->error('Something items can\'t updated or inserted');
            }
        } while ($pagination->nextPage());
    }
}
