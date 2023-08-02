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
        Schema::create('expired_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('count');
            $table->timestamp('checked_at');
            $table->string('type');
            $table->string('token', 10)->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expired_subscriptions');
    }
};
