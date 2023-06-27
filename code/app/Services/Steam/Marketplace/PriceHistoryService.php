<?php

namespace App\Services\Steam\Marketplace;

use App\Entities\PriceHistoryDenormalizedEntity;
use App\Entities\PriceHistoryEntity;
use App\Exceptions\UnauthorizedUserException;
use App\Helpers\TempTableHelper;
use App\Models\ItemModel;
use App\Models\Temp\PricesHistoryTempModel;
use App\Services\Steam\AuthService;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\DB;

class PriceHistoryService extends AuthService {

    private const MARKETPLACE_PRICE_HISTORY_ENDPOINT = '/market/pricehistory/';

    // get price history for specific item
    // array is temporary -> maybe return entity in feature
    // get items of prices ..

    public function fetchItemPrices(ItemModel $item): array {
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
    public function updateItemPrices(ItemModel $item, array $prices)
    {

    }

}
