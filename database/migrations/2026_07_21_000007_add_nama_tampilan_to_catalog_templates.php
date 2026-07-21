<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalog_templates', function (Blueprint $table) {
            $table->string('nama_tampilan', 100)->nullable()->after('kategori');
        });
    }

    public function down(): void
    {
        Schema::table('catalog_templates', function (Blueprint $table) {
            $table->dropColumn('nama_tampilan');
        });
    }
};
