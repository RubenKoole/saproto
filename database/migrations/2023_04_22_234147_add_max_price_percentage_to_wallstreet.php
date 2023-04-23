<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxPricePercentageToWallstreet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wallstreet_drink', function (Blueprint $table) {
            $table->integer('max_price_percentage')->default(100);
            $table->renameColumn('price_decrease', 'price_decrease_percentage');
            $table->renameColumn('price_increase', 'price_increase_percentage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wallstreet_drink', function (Blueprint $table) {
            $table->dropColumn('max_price_percentage');
            $table->renameColumn('price_decrease_percentage', 'price_decrease');
            $table->renameColumn('price_increase_percentage', 'price_increase');
        });
    }
}
