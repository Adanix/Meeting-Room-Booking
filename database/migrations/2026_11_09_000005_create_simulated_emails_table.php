<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulated_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->cascadeOnDelete();
            $table->string('event_type', 40);
            $table->string('recipient_type', 20); // organizer | office
            $table->string('recipient_email', 150)->index();
            $table->string('recipient_name', 100);
            $table->string('subject', 200);
            $table->string('locale', 10)->default('en');
            $table->longText('body_html');
            $table->text('body_text');
            $table->string('manage_url', 255)->nullable();
            $table->string('ics_url', 255)->nullable();
            $table->text('google_calendar_url')->nullable();
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulated_emails');
    }
};
