<?php

namespace App\Models\Temp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class PricesHistoryTempModel extends Model
{
    use HasFactory;

    protected $table = 'prices_history_temp';
    protected $fillable = ['date', 'median_price', 'id_currency'];
    public $timestamps = false;

    /**
     * Return builder query for
     * get median prices group by date
     *
     * @return Builder
     */
    public static function getMedianPrices(): Builder
    {
        return DB::query()->fromSub(function(Builder $query) {
            $query->selectRaw('
                `date`,
                `id_currency`,
                `currencies`.`rate` AS currency_rate,
                MEDIAN(median_price) OVER (PARTITION BY date) AS price,
                ROW_NUMBER() OVER (PARTITION BY date) AS volume'
            )
                ->from('prices_history_temp')
                ->join('currencies', 'prices_history_temp.id_currency', '=', 'currencies.id')
                ->orderByDesc('volume');
        }, 'prices_history_by_date')
            ->selectRaw('
                `date`,
                TRUNCATE (price / currency_rate, 2) AS converted_price,
                `volume`'
            )
            ->groupBy('date');
    }
}
