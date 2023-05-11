<?php

namespace App\Services\Steam;

use App\Classes\Pagination;
use App\Classes\Filter\Filter;
use App\Entities\ItemEntity;
use App\Entities\PriceHistoryDenormalizedEntity;
use App\Entities\PriceHistoryEntity;
use App\Exceptions\UnauthorizedUserException;
use App\Helpers\TempTableHelper;
use App\Models\ItemModel;
use App\Models\Temp\PricesHistoryTempModel;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
    public function fetchPriceHistory(ItemModel $item): array {
        try {
            $history = $this->client->get(self::MARKETPLACE_PRICE_HISTORY_ENDPOINT, [
                'query' => [
                    'appid' => $item->id_app,
                    'market_hash_name' => $item->market_hash_name
                ]
            ]);

             $body = json_decode($history->getBody());

            // temporary fetching from file
            // $body = json_decode(Storage::disk('local')->get('test/price_history/test_item.json'));

            $denormalizedPrices = array_reduce($body->prices, function ($result, $item) {
                $priceHistory = new PriceHistoryEntity();
                $priceHistory->date = $priceHistory::getDateFromSteamTimestamp($item[0]);
                $priceHistory->medianPrice = $item[1];
                $priceHistory->volume = (int)$item[2];

                return array_merge(
                    $result,
                    PriceHistoryDenormalizedEntity::fromPriceHistory($priceHistory)
                );
            }, []);

            $pricesHistoryTemp = new TempTableHelper('prices_history_temp');
            $pricesHistoryTemp->recreate();

            DB::transaction(function () use ($denormalizedPrices) {
                foreach ($denormalizedPrices as $price)
                {
                    $price->toModel()->save();
                }
            });

            $medianPrices = PricesHistoryTempModel::getMedianPrices();

            return $medianPrices->get()->map(function ($price) {
                $entity = new PriceHistoryEntity();
                $entity->date = Carbon::createFromFormat('Y-m-d', $price->date)->setTime(0, 0);
                $entity->medianPrice = $price->converted_price;
                $entity->volume = $price->volume;

                return $entity;
            })->toArray();

        } catch (RequestException $e) {
            report($e);
            if ($e->getResponse()->getStatusCode() === 400) {
                throw new UnauthorizedUserException($this->profileAlias);
            }
            throw $e;
        }
    }

    // update price history for specific item in database
    public function updatePriceHistory(ItemModel $item, array $prices)
    {

    }
}
