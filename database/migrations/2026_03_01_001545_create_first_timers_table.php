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
        Schema::create('first_timers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_id')->constrained()->onDelete('cascade');
            $table->foreignId('bringer_id')->nullable()->constrained()->onDelete('set null');
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('primary_contact')->unique();
            $table->string('alternate_contact')->nullable();
            $table->string('gender');
            $table->string('date_of_birth');
            $table->text('residential_address');
            $table->string('occupation')->nullable();
            $table->date('date_of_visit');
            $table->string('marital_status');
            $table->boolean('born_again')->default(false);
            $table->boolean('water_baptism')->default(false);
            $table->text('prayer_requests')->nullable();
            $table->integer('service_count')->default(0);
            $table->boolean('locked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('first_timers');
    }
};
