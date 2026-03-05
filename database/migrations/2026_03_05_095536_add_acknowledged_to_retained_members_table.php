<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('retained_members', function (Blueprint $table) {
            $table->boolean('acknowledged')->default(false)->after('locked');
        });

        // Mark all existing retained members as acknowledged
        DB::table('retained_members')->update(['acknowledged' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retained_members', function (Blueprint $table) {
            $table->dropColumn('acknowledged');
        });
    }
};
