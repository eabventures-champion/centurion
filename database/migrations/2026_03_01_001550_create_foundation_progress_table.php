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
        Schema::create('foundation_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('foundation_class_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('first_timer_id')->nullable();
            $table->unsignedBigInteger('retained_member_id')->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('marked_by'); // official_id
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foundation_progress');
    }
};
