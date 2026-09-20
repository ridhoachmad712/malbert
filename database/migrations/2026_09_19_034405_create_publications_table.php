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
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('slug')->unique();
            $table->json('content');
            $table->json('published_content')->nullable();
            $table->string('status')->default('draft')->index();
            $table->string('review_status')->default('draft')->index();
            $table->text('review_note')->nullable();
            $table->boolean('featured')->default(false);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('guru');
            $table->boolean('active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publications');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['role', 'active']));
    }
};
