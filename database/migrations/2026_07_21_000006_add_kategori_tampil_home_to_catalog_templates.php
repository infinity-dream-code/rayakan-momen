<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalog_templates', function (Blueprint $table) {
            $table->string('kategori', 50)->nullable()->after('template_key');
            $table->boolean('tampil_home')->default(false)->after('aktif_katalog');
        });
    }

    public function down(): void
    {
        Schema::table('catalog_templates', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'tampil_home']);
        });
    }
};
