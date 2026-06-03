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
        Schema::table('users', function (Blueprint $table) {
            // --- 1. IDENTIFICATION ---
            if (!Schema::hasColumn('users', 'uuid')) {
                $table->uuid('uuid')->unique()->after('id')->nullable();
            }
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->nullable()->after('uuid');
            }

            // --- 2. PROFILE ---
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('users', 'birthday')) {
                $table->date('birthday')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other', 'unknown'])->default('unknown')->after('birthday');
            }

            // --- 3. SECURITY & AUTH ---
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                $table->text('two_factor_secret')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            }

            // --- 4. SOCIAL LOGIN ---
            if (!Schema::hasColumn('users', 'provider')) {
                $table->string('provider')->nullable()->after('locked_until');
            }
            if (!Schema::hasColumn('users', 'provider_id')) {
                $table->string('provider_id')->nullable()->after('provider');
            }

            // --- 5. FLEXIBILITY ---
            if (!Schema::hasColumn('users', 'settings')) {
                $table->json('settings')->nullable()->after('provider_id');
            }
            if (!Schema::hasColumn('users', 'metadata')) {
                $table->json('metadata')->nullable()->after('settings');
            }

            // --- 6. ACTIVATION (For existing logic) ---
            if (!Schema::hasColumn('users', 'key')) {
                $table->string('key')->nullable()->after('metadata');
            }
            if (!Schema::hasColumn('users', 'keyTime')) {
                $table->timestamp('keyTime')->nullable()->after('key');
            }

            // --- 7. SOFT DELETES ---
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'uuid', 'username', 'first_name', 'last_name', 
                'birthday', 'gender', 'two_factor_secret', 
                'two_factor_recovery_codes', 'provider', 'provider_id', 
                'settings', 'metadata', 'deleted_at'
            ]);
        });
    }
};
