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
        Schema::table('first_timers', function (Blueprint $table) {
            $table->foreignId('church_id')->nullable()->change();
            $table->foreignId('pcf_id')->nullable()->after('church_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('first_timers', function (Blueprint $table) {
            $table->dropForeign(['pcf_id']);
            $table->dropColumn('pcf_id');
            $table->foreignId('church_id')->nullable(false)->change();
        });
    }
};
