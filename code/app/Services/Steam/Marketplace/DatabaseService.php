<?php

namespace App\Services\Steam\Marketplace;

use App\Classes\Filter\Filter;
use App\Classes\Pagination;
use App\Entities\ItemEntity;
use App\Models\ItemModel;
use App\Services\Steam\AuthService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseService extends AuthService {

    private const ITEM_NOT_TRADABLE = 0;
    private const ITEMS_ENDPOINT = 'market/search/render/';

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

        $items = $this->client->get(self::ITEMS_ENDPOINT, [
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
}

