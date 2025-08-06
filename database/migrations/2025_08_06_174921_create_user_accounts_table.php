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
        Schema::create('user_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()->primary();
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->string('phone', 20)->unique();
            $table->string('password', 255);
            $table->string('user_type', 50);
            $table->string('gender', 20)->nullable();
            $table->string('gst_no', 30)->nullable();
            $table->string('otp', 10)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('status', ['1', '2'])->default('1')->comment('1->Active, 2->Inactive');
            $table->string('referral_code', 20)->nullable();
            $table->string('referral_by', 20)->nullable();
            $table->enum('is_verified', ['1', '2'])->default('1')->comment('1->Verified, 2->Not Verified');
            $table->text('product_and_services')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_accounts');
    }
};
