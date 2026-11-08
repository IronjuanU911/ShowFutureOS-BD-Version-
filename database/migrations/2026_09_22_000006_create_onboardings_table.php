<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboardings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->boolean('communication_rules_sent')->default(false);
            $table->boolean('calendar_shared')->default(false);
            $table->date('started_at')->nullable();
            $table->date('target_completion_date')->nullable();
            $table->integer('progress_percentage')->default(0);
            $table->date('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('communication_channel')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboardings');
    }
};
