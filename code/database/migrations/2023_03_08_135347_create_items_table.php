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
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('id_app');
            $table->foreign('id_app')
                ->references('id')->on('apps')
                ->cascadeOnDelete();

            $table->foreignId('id_class');
            $table->foreignId('id_instance');

            $table->string('market_hash_name', 250);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
