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
        Schema::create('prices_history', function (Blueprint $table) {
            $table->primary(['id_market', 'id_item', 'date'], 'id');

            $table->unsignedInteger('id_market');
            $table->foreign('id_market')
                ->references('id')->on('markets')
                ->cascadeOnDelete();

            $table->unsignedInteger('id_item');
            $table->foreign('id_item')
                ->references('id')->on('items')
                ->cascadeOnDelete();

            $table->date('date')->comment('Date of sell');
            $table->decimal('median_price', 6, 2);
            $table->unsignedTinyInteger('volume')->comment('Count of sales');

            $table->comment(
                Arr::join([
                    'History of item prices',
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
        Schema::dropIfExists('prices_history');
    }
};
