<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin')->after('email')->index();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('whatsapp', 32)->unique();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('playstation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('specifications')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('playstation_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playstation_type_id')->constrained()->restrictOnDelete();
            $table->string('code', 32)->unique();
            $table->string('name');
            $table->string('condition')->default('Good');
            $table->string('status')->default('available')->index();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['playstation_type_id', 'status']);
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playstation_type_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('duration_minutes');
            $table->decimal('price', 12, 2);
            $table->text('description')->nullable();
            $table->json('facilities')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamps();

            $table->index(['playstation_type_id', 'is_active']);
        });

        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('genre')->nullable()->index();
            $table->string('platform')->nullable();
            $table->string('player_count', 32)->nullable();
            $table->string('cover')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('game_playstation_unit', function (Blueprint $table) {
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('playstation_unit_id')->constrained()->cascadeOnDelete();
            $table->primary(['game_id', 'playstation_unit_id']);
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('booking_number', 32)->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('playstation_unit_id')->constrained()->restrictOnDelete();
            $table->foreignId('package_id')->constrained()->restrictOnDelete();
            $table->dateTime('start_at')->index();
            $table->dateTime('end_at')->index();
            $table->unsignedInteger('duration_minutes');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->string('status')->default('pending')->index();
            $table->text('notes')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['playstation_unit_id', 'start_at', 'end_at']);
            $table->index(['status', 'start_at']);
        });

        Schema::create('booking_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('package_name');
            $table->string('playstation_type');
            $table->string('unit_code', 32);
            $table->string('unit_name');
            $table->string('customer_name');
            $table->string('customer_whatsapp', 32);
            $table->string('customer_email')->nullable();
            $table->decimal('package_price', 12, 2);
            $table->unsignedInteger('duration_minutes');
            $table->decimal('total_price', 12, 2);
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->json('facilities')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 40)->unique();
            $table->foreignId('booking_id')->constrained()->restrictOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('method')->index();
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('pending')->index();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('paid_at')->nullable()->index();
            $table->timestamps();

            $table->index(['booking_id', 'status']);
        });

        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('playstation_unit_id')->constrained()->restrictOnDelete();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('status')->default('reserved')->index();
            $table->timestamps();

            $table->index(['playstation_unit_id', 'start_at', 'end_at']);
            $table->index(['status', 'start_at']);
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('type')->default('string');
            $table->string('group')->default('general')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('booking_details');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('game_playstation_unit');
        Schema::dropIfExists('games');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('playstation_units');
        Schema::dropIfExists('playstation_types');
        Schema::dropIfExists('customers');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
