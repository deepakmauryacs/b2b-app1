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
        Schema::create('buy_requirements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('product_name');
            $table->string('country_code', 5);
            $table->string('mobile_number', 20);
            $table->date('expected_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buy_requirements');
    }
};
