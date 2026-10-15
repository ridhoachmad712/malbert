<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->string('label', 80);
            $table->string('type', 20);
            $table->string('target', 2048)->nullable();
            $table->unsignedBigInteger('site_page_id')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->boolean('open_new_tab')->default(false);
            $table->timestamps();
        });

        DB::table('navigation_items')->insert([
            ['label' => 'Portofolio', 'type' => 'internal', 'target' => 'portfolio', 'sort_order' => 10, 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Pojok Baca', 'type' => 'internal', 'target' => 'readings.public', 'sort_order' => 20, 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Tentang Sekolah', 'type' => 'internal', 'target' => 'profile', 'sort_order' => 30, 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigation_items');
    }
};
