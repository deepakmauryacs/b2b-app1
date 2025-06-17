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
        Schema::create('vendor_exports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('range_start');
            $table->unsignedBigInteger('range_end');
            $table->string('status')->default('in_progress');
            $table->string('file_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_exports');
    }
};
