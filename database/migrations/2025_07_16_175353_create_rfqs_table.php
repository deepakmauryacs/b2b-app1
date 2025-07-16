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
        Schema::create('rfqs', function (Blueprint $table) {

            $table->id(); // Primary key, auto-incrementing ID
            $table->string('rfq_id')->unique(); // Unique string for RFQ identifier (e.g., UUID or custom code)
            $table->unsignedBigInteger('buyer_id'); // Foreign key for the buyer (assuming 'users' table)
            $table->string('product_name');
            $table->text('product_specification')->nullable(); // Nullable if not always required
            $table->decimal('quantity', 10, 2); // Decimal for quantity, e.g., 10 total digits, 2 after decimal
            $table->string('measurement_unit');
            $table->unsignedBigInteger('user_id'); // Foreign key for the user who created/managed the RFQ
            $table->timestamps(); // created_at and updated_at columns
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfqs');
    }
};
