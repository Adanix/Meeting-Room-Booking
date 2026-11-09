<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference_code', 24)->unique();
            $table->string('management_token', 64)->unique();
            $table->foreignId('room_id')->constrained('rooms')->restrictOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('booking_date');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->unsignedSmallInteger('duration_minutes');
            $table->string('title', 120);
            $table->text('agenda')->nullable();
            $table->string('organizer_name', 100);
            $table->string('organizer_email', 150)->index();
            $table->unsignedSmallInteger('attendees_count');
            $table->string('status', 20)->default('confirmed');
            $table->string('locale', 10)->default('en');
            $table->string('cancellation_reason', 255)->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->string('cancelled_by', 50)->nullable();
            $table->timestamps();

            $table->index(['room_id', 'status', 'start_at', 'end_at'], 'idx_bookings_room_overlap');
            $table->index(['booking_date', 'status'], 'idx_bookings_date_status');
        });

        Schema::create('booking_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 30);
            $table->json('previous_state')->nullable();
            $table->json('new_state')->nullable();
            $table->string('note', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_status_histories');
        Schema::dropIfExists('bookings');
    }
};
