<?php

namespace App\Console\Commands\Steam;

use App\Classes\Filter\Steam\CSGOFilter;
use App\Classes\Pagination;
use App\Services\Steam\ItemsService;
use App\Traits\Env\SteamUsersTrait;
use Illuminate\Console\Command;

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
        $x = new ItemsService($userAlias); // add $appId instead of 730 by hardcode

        $filter = CSGOFilter::getMarketplaceItems();
        $pagination = new Pagination(null, 0, 10); // 10 > 100
        $pagination->addRateLimiter(5);

        // save by 100 items as transaction!

        do {
            $this->confirm('press yes'); // just temporary

            $f = $x->fetchItems($filter, $pagination);
            // add guzzle throws. wrap another page (not all pages)

            // $f[0]->toModel()->save();
            // something do with fetched items
        } while ($pagination->nextPage());
    }
}
