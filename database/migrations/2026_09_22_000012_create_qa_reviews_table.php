<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qa_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewed_by')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_approved')->default(false);
            // QA Estratégico
            $table->boolean('strategic_message_aligned')->default(false);
            $table->boolean('strategic_cta_clear')->default(false);
            $table->boolean('strategic_funnel_correct')->default(false);
            // QA Marca & Compliance
            $table->boolean('brand_tone_correct')->default(false);
            $table->boolean('brand_no_risky_claims')->default(false);
            $table->boolean('brand_policy_aligned')->default(false);
            // QA Técnico
            $table->boolean('tech_format_correct')->default(false);
            $table->boolean('tech_subtitles_legible')->default(false);
            $table->boolean('tech_duration_adequate')->default(false);
            // Resultado
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qa_reviews');
    }
};
