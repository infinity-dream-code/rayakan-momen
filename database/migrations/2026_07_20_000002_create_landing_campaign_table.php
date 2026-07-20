<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_campaign', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->boolean('aktif')->default(false);
            $table->string('image_url', 500)->nullable();
            $table->string('cloudinary_public_id', 255)->nullable();
            $table->timestamps();
        });

        DB::table('landing_campaign')->insert([
            'id' => 1,
            'aktif' => false,
            'image_url' => null,
            'cloudinary_public_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_campaign');
    }
};
