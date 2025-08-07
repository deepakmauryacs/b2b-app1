<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('role_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('module_id')->after('role_id');
            $table->boolean('can_delete')->default(false)->after('can_view');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
        });

        Schema::table('role_permissions', function (Blueprint $table) {
            $table->dropColumn(['module', 'can_export']);
        });
    }

    public function down(): void
    {
        Schema::table('role_permissions', function (Blueprint $table) {
            $table->string('module');
            $table->boolean('can_export')->default(false);
            $table->dropForeign(['module_id']);
            $table->dropColumn(['module_id', 'can_delete']);
        });
    }
};
