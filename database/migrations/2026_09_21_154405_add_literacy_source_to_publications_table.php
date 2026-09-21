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
        Schema::table('publications', function (Blueprint $table) {
            $table->foreignId('literacy_submission_id')->nullable()->unique()->after('user_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('source_revision_number')->nullable()->after('literacy_submission_id');
            $table->json('source_snapshot')->nullable()->after('source_revision_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('literacy_submission_id');
            $table->dropColumn(['source_revision_number', 'source_snapshot']);
        });
    }
};
