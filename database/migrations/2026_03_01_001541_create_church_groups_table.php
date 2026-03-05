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
        Schema::create('church_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_category_id')->constrained()->onDelete('cascade');
            $table->string('group_name');
            $table->string('pastor_name');
            $table->string('pastor_contact')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('church_groups');
    }
};
