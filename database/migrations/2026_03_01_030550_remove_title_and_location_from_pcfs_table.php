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
        Schema::table('pcfs', function (Blueprint $table) {
            $table->dropColumn(['title', 'location']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pcfs', function (Blueprint $table) {
            $table->enum('title', ['Bro', 'Sis', 'Pastor', 'Dcn', 'Dcns', 'Mr', 'Mrs'])->after('name');
            $table->string('location')->nullable()->after('leader_contact');
        });
    }
};
