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
        Schema::create('help_supports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('user_type', ['vendor', 'buyer']);
            $table->string('name');
            $table->string('contact_no', 20);
            $table->string('email');
            $table->text('message');
            $table->string('attachment')->nullable();
            $table->text('reply_message')->nullable();
            $table->enum('status', ['open', 'pending', 'on_hold', 'solved', 'closed'])->default('open');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('help_supports');
    }
};
