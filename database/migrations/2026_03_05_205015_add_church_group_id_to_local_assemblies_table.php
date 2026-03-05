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
        Schema::table('local_assemblies', function (Blueprint $table) {
            $table->foreignId('church_group_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('local_assemblies', function (Blueprint $table) {
            $table->dropForeign(['church_group_id']);
            $table->dropColumn('church_group_id');
        });
    }
};
