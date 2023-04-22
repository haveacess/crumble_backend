<?php

namespace App\Services\Steam;

use App\Classes\Pagination;
use App\Classes\Filter\Filter;
use App\Entities\ItemEntity;
use App\Exceptions\NotFoundEntityException;
use App\Models\AppsModel;
use App\Models\ItemModel;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemsService extends AuthService {

    private const ITEM_NOT_TRADABLE = 0;

    private const MARKETPLACE_ITEMS_ENDPOINT = 'market/search/render/';
    private const MARKETPLACE_PRICE_HISTORY_ENDPOINT = '/market/pricehistory/';

    /**
     * Receiving list of items on marketplace
     *
     * @param Filter $itemsFilter
     * @param Pagination $pagination
     * @return ItemEntity[]
     * @throws GuzzleException Cannot to fetch this item list
     */
    public function fetchItems(Filter $itemsFilter, Pagination $pagination): array
    {
        $paginationQuery = [
            'start' => $pagination->getOffset(),
            'count' => $pagination->getItemsCount()
        ];

        $query = array_merge($paginationQuery, $itemsFilter->toArray());

        $items = $this->client->get(self::MARKETPLACE_ITEMS_ENDPOINT, [
            'query' => $query
        ]);

        $body = json_decode($items->getBody());

        if ($pagination->isFirstPage()) {
            $pagination->setTotalItemsCount($body->total_count);
        }

        return array_reduce($body->results, function ($result, $item) {
            $asset = $item->asset_description;

            if ($asset->tradable === self::ITEM_NOT_TRADABLE) {
                Log::alert('Item was skipped because isn\'t tradable', [
                    'item' => $asset->market_hash_name
                ]);
                return $result;
            }

            $entity = new ItemEntity();
            $entity->idApp = $asset->appid;
            $entity->idClass = $asset->classid;
            $entity->idInstance = $asset->instanceid;
            $entity->marketHashName = $asset->market_hash_name;

            $result[] = $entity;
            return $result;
        }, []);
    }

    /**
     * Updating items in database <br>
     * All the operations will be in transaction
     *
     * @param ItemEntity[] $items List of items need to be pushed
     * @throws QueryException Failed updating or creating new items.
     * The transaction was canceled
     * @return bool Return true if all right
     */
    public function updateItems(array $items): bool {
        DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                $model = ItemModel::where([
                    'id_class' => $item->idClass,
                    'id_instance' => $item->idInstance
                ])->limit(1);

                if ($model->exists()) {
                    $model->touch(); // just mark as update
                    continue;
                }

                $item->toModel()->save();
            }
        });

        return true;
    }

    // get price history for specific item
    // array is temporary -> maybe return entity in feature
    public function getPriceHistory(ItemModel $item): array {
        $history = $this->client->get(self::MARKETPLACE_ITEMS_ENDPOINT, [
            'query' => [
                'appId' => $item->id_app,
                'market_hash_name' => $item->market_hash_name
            ]
        ]);

        $body = json_decode($history->getBody());
        echo $body;

        return [];
    }

    // update price history for specific item in database
    public function updatePriceHistory(ItemModel $item, array $prices)
    {

    }
}
