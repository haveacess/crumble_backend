<?php

use App\Classes\Restriction\LowerCase;
use App\Classes\Restriction\UpperCase;
use App\Traits\Database\RestrictionsTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use RestrictionsTrait;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 3);
            $table->decimal('rate', 5, 2)->comment('to 1 USD');
            $table->string('suffix', 4);
        });

        $this->addRestriction('currencies', new UpperCase('name'));
        $this->addRestriction('currencies', new LowerCase('suffix'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
