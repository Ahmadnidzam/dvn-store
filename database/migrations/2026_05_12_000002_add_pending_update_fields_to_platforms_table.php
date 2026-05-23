<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom untuk fitur "Update File": developer upload file baru ke
     * pending_file_path → di-scan VirusTotal → kalau clean, swap dengan file_path
     * (file lama dihapus). User di library dapat badge "Update tersedia" yang
     * hilang otomatis setelah re-download.
     */
    public function up(): void
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->string('pending_file_path', 255)->nullable()->after('file_size');
            $table->unsignedBigInteger('pending_file_size')->default(0)->after('pending_file_path');
            $table->enum('pending_scan_status', ['pending', 'scanning', 'clean', 'infected', 'error'])
                  ->nullable()->after('pending_file_size');
            $table->json('pending_scan_result')->nullable()->after('pending_scan_status');
            $table->timestamp('pending_uploaded_at')->nullable()->after('pending_scan_result');
            $table->timestamp('file_updated_at')->nullable()->after('pending_uploaded_at');
        });
    }

    public function down(): void
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->dropColumn([
                'pending_file_path',
                'pending_file_size',
                'pending_scan_status',
                'pending_scan_result',
                'pending_uploaded_at',
                'file_updated_at',
            ]);
        });
    }
};
