<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalog_templates', function (Blueprint $table) {
            $table->string('preview_image_url', 500)->nullable()->after('aktif_katalog');
            $table->string('preview_cloudinary_id', 255)->nullable()->after('preview_image_url');
        });
    }

    public function down(): void
    {
        Schema::table('catalog_templates', function (Blueprint $table) {
            $table->dropColumn(['preview_image_url', 'preview_cloudinary_id']);
        });
    }
};
