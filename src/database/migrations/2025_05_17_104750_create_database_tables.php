<?php

use Database\Seeders\FrequencySeeder;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });


        Schema::create('frequencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('interval_minutes');
            $table->timestamps();
        });
        $this->seedFrequencies();


        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
            $table->foreignId('frequency_id')->constrained('frequencies')->onDelete('cascade');
            $table->enum('status', ['pending', 'active', 'canceled'])->default('pending');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('subscription_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->onDelete('cascade');
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamp('next_scheduled_at')->nullable();
            $table->enum('status', ['success', 'error', 'queued']);
            $table->timestamps();
        });

        Schema::create('subscription_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->onDelete('cascade');
            $table->string('token')->unique();
            $table->enum('type', ['confirm', 'cancel']);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('frequencies');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_emails');
        Schema::dropIfExists('subscription_tokens');
    }

    private function seedFrequencies(): void
    {
        $seeder = new FrequencySeeder();
        $seeder->run();
    }
};
