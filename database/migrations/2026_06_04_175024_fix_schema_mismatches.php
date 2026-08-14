<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ====== CONTRACTS ======
        Schema::table('contracts', function (Blueprint $table) {
            if (! Schema::hasColumn('contracts', 'contract_number')) {
                $table->string('contract_number')->nullable()->after('uuid');
            }
            if (! Schema::hasColumn('contracts', 'content')) {
                $table->longText('content')->nullable()->after('contract_number');
            }
            if (! Schema::hasColumn('contracts', 'file_path')) {
                $table->string('file_path')->nullable()->after('content');
            }
            if (! Schema::hasColumn('contracts', 'signed_by_buyer')) {
                $table->boolean('signed_by_buyer')->default(false)->after('contract_pdf_path');
            }
            if (! Schema::hasColumn('contracts', 'signed_by_seller')) {
                $table->boolean('signed_by_seller')->default(false)->after('signed_by_buyer');
            }
            if (! Schema::hasColumn('contracts', 'signed_at')) {
                $table->timestamp('signed_at')->nullable()->after('signed_by_seller');
            }
        });

        if (Schema::hasColumn('contracts', 'contract_pdf_path')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->renameColumn('contract_pdf_path', 'pdf_path');
            });
        }

        // ====== SUBSCRIPTIONS ======
        Schema::table('subscriptions', function (Blueprint $table) {
            if (! Schema::hasColumn('subscriptions', 'name')) {
                $table->string('name')->nullable()->after('uuid');
            }
            if (! Schema::hasColumn('subscriptions', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (! Schema::hasColumn('subscriptions', 'duration_days')) {
                $table->integer('duration_days')->default(30)->after('description');
            }
        });

        // ====== AI_SCANNING_JOBS ======
        Schema::table('ai_scanning_jobs', function (Blueprint $table) {
            if (! Schema::hasColumn('ai_scanning_jobs', 'asset_id')) {
                $table->foreignId('asset_id')->nullable()->constrained('3d_assets')->nullOnDelete()->after('store_id');
            }
            if (! Schema::hasColumn('ai_scanning_jobs', 'result')) {
                $table->json('result')->nullable()->after('status');
            }
        });

        // ====== ADD amount_paid to store_subscriptions ======
        Schema::table('store_subscriptions', function (Blueprint $table) {
            if (! Schema::hasColumn('store_subscriptions', 'amount_paid')) {
                $table->decimal('amount_paid', 15, 2)->default(0)->after('payment_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $drop = ['contract_number', 'content', 'file_path', 'signed_by_buyer', 'signed_by_seller', 'signed_at'];
            foreach ($drop as $col) {
                if (Schema::hasColumn('contracts', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
        if (Schema::hasColumn('contracts', 'pdf_path')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->renameColumn('pdf_path', 'contract_pdf_path');
            });
        }
        Schema::table('subscriptions', function (Blueprint $table) {
            $drop = ['name', 'description', 'duration_days'];
            foreach ($drop as $col) {
                if (Schema::hasColumn('subscriptions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
        Schema::table('ai_scanning_jobs', function (Blueprint $table) {
            if (Schema::hasColumn('ai_scanning_jobs', 'asset_id')) {
                $table->dropColumn('asset_id');
            }
            if (Schema::hasColumn('ai_scanning_jobs', 'result')) {
                $table->dropColumn('result');
            }
        });
        Schema::table('store_subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('store_subscriptions', 'amount_paid')) {
                $table->dropColumn('amount_paid');
            }
        });
    }
};
