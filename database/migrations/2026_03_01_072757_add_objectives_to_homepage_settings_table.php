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
        Schema::table('homepage_settings', function (Blueprint $table) {
            $table->string('objectives_title')->default('Our Aim & Objectives');
            $table->text('objectives_subtitle')->nullable();

            $table->string('obj_1_title')->default('Nurturing Souls');
            $table->text('obj_1_description')->nullable();

            $table->string('obj_2_title')->default('Foundation School');
            $table->text('obj_2_description')->nullable();

            $table->string('obj_3_title')->default('Membership Integration');
            $table->text('obj_3_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homepage_settings', function (Blueprint $table) {
            $table->dropColumn([
                'objectives_title',
                'objectives_subtitle',
                'obj_1_title',
                'obj_1_description',
                'obj_2_title',
                'obj_2_description',
                'obj_3_title',
                'obj_3_description'
            ]);
        });
    }
};
