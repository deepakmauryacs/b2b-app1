<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('banner_img');
            $table->string('banner_link')->nullable();
            $table->date('banner_start_date')->nullable();
            $table->date('banner_end_date')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('banner_type')->default(1); // 1->home slider
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
