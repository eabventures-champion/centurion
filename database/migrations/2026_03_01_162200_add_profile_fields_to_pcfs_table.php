<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pcfs', function (Blueprint $table) {
            $table->string('gender')->nullable()->after('leader_contact');
            $table->string('marital_status')->nullable()->after('gender');
            $table->string('occupation')->nullable()->after('marital_status');
        });
    }

    public function down(): void
    {
        Schema::table('pcfs', function (Blueprint $table) {
            $table->dropColumn(['gender', 'marital_status', 'occupation']);
        });
    }
};
