<?php

namespace App\Entities;

use App\Models\Temp\PricesHistoryTempModel;
use Carbon\Carbon;

class PriceHistoryDenormalizedEntity {

    public Carbon $date;
    public float $medianPrice;
    public int $idCurrency;

    /**
     * Getting array of denormalized entities
     * based on PriceHistoryEntity
     *
     * @param PriceHistoryEntity $priceHistory
     * @return PriceHistoryDenormalizedEntity[]
     */
    public static function fromPriceHistory(PriceHistoryEntity $priceHistory): array
    {
        $entities = [];
        $volume = $priceHistory->volume;

        while ($volume--)
        {
            $denormalizedEntity = new PriceHistoryDenormalizedEntity();
            $denormalizedEntity->idCurrency = 5; // temporary mock (!!)
            $denormalizedEntity->medianPrice = $priceHistory->medianPrice;
            $denormalizedEntity->date = $priceHistory->date;

            $entities[] = $denormalizedEntity;
        }

        return $entities;
    }

    /**
     * Get entity as model
     *
     * @return PricesHistoryTempModel
     */
    public function toModel(): PricesHistoryTempModel {
        return new PricesHistoryTempModel([
            'date' => $this->date->format('Y-m-d'),
            'median_price' => $this->medianPrice,
            'id_currency' => $this->idCurrency
        ]);
    }
}
