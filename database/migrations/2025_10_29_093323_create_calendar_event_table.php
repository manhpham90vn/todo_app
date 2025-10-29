<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calendar_events', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('calendar_id');
            $t->string('google_event_id')->index();
            $t->string('status')->nullable();
            $t->string('summary')->nullable();
            $t->text('description')->nullable();
            $t->string('location')->nullable();
            $t->timestamp('start_at')->nullable();
            $t->timestamp('end_at')->nullable();
            $t->json('attendees')->nullable();
            $t->json('raw')->nullable();
            $t->timestamps();

            $t->unique(['calendar_id', 'google_event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
