<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prices_orders', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('id_market');
            $table->foreign('id_market')
                ->references('id')->on('markets')
                ->cascadeOnDelete();

            $table->unsignedInteger('id_item');
            $table->foreign('id_item')
                ->references('id')->on('items')
                ->cascadeOnDelete();

            $table->decimal('price', 6)
                ->comment('price which need to pay for buy item');

            $table->unsignedSmallInteger('count')
                ->comment('count orders for this price');

            $table->dateTime('update_at')
                ->comment('When data last fetched');

            $table->comment(
                Arr::join([
                    'Only actual (last fetched) data.',
                    'When new data for this market:item will received - old data will be deleted.',
                    'All prices represent in USD'
                ], PHP_EOL)
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices_orders');
    }
};
