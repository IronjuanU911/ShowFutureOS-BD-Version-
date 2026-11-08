<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QaReview extends Model
{
    protected $fillable = [
        'ticket_id',
        'reviewed_by',
        'is_approved',
        'strategic_message_aligned',
        'strategic_cta_clear',
        'strategic_funnel_correct',
        'brand_tone_correct',
        'brand_no_risky_claims',
        'brand_policy_aligned',
        'tech_format_correct',
        'tech_subtitles_legible',
        'tech_duration_adequate',
        'rejection_reason',
        'notes',
        'reviewed_at',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'strategic_message_aligned' => 'boolean',
        'strategic_cta_clear' => 'boolean',
        'strategic_funnel_correct' => 'boolean',
        'brand_tone_correct' => 'boolean',
        'brand_no_risky_claims' => 'boolean',
        'brand_policy_aligned' => 'boolean',
        'tech_format_correct' => 'boolean',
        'tech_subtitles_legible' => 'boolean',
        'tech_duration_adequate' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getChecklistProgressAttribute(): int
    {
        $checks = [
            $this->strategic_message_aligned,
            $this->strategic_cta_clear,
            $this->strategic_funnel_correct,
            $this->brand_tone_correct,
            $this->brand_no_risky_claims,
            $this->brand_policy_aligned,
            $this->tech_format_correct,
            $this->tech_subtitles_legible,
            $this->tech_duration_adequate,
        ];
        return count(array_filter($checks));
    }
}
