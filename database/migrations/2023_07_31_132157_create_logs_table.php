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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('recipient', 200);
            $table->string('subject', 200)->nullable();
            $table->text('body')->nullable();
            $table->string('type', 100);
            $table->string('notification', 100);
            $table->json('details')->nullable();
            $table->timestamp('saved_at');
            $table->timestamp('sent_at')->nullable();
            $table->string('error_message', 200)->nullable();
            $table->boolean('is_sent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
