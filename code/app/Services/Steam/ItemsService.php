<?php

namespace App\Services\Steam;

use App\Classes\Pagination;
use App\Classes\Filter\Filter;
use App\Entities\ItemEntity;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ItemsService extends AuthService {

    private int $appId = 730; // set in construct
    // add validation by model in construct

    private const MARKETPLACE_ITEMS_ENDPOINT = 'market/search/render/';

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

            if ($asset->tradable === 0) {
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

    // somewhere send items to database (tranasction!! by chunk XX items)
}
