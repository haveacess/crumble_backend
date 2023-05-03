<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prices_history_temp', function (Blueprint $table) {
            $table->temporary();

            $table->increments('id');

            $table->date('date')->comment('Date of sell');
            $table->decimal('median_price', 6, 2);

            $table->unsignedInteger('id_currency');
            $table->foreign('id_currency')
                ->references('id')->on('currencies')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices_history_temp');
    }
};
