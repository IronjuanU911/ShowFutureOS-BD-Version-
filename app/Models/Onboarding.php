<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Onboarding extends Model
{
    protected $fillable = [
        'client_id',
        'status',
        'communication_rules_sent',
        'calendar_shared',
        'started_at',
        'target_completion_date',
        'progress_percentage',
        'completed_at',
        'notes',
        'communication_channel',
    ];

    protected $casts = [
        'communication_rules_sent' => 'boolean',
        'calendar_shared' => 'boolean',
        'started_at' => 'date',
        'target_completion_date' => 'date',
        'completed_at' => 'date',
        'progress_percentage' => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OnboardingChecklistItem::class)->orderBy('day_number')->orderBy('order');
    }

    public function checklistItems(): HasMany
    {
        return $this->items();
    }

    public function completedItems(): HasMany
    {
        return $this->hasMany(OnboardingChecklistItem::class)->where('is_completed', true);
    }

    public function getProgressPercentageAttribute($value): int
    {
        // Si el valor ya está almacenado en la columna de la BD, úsalo directamente (evita N+1 queries)
        if ($value !== null) {
            return (int) $value;
        }

        // Si la relación ya está cargada en memoria, calcula sin consultar la BD
        if ($this->relationLoaded('items')) {
            $total = $this->items->count();
            if ($total === 0) return 0;
            $completed = $this->items->where('is_completed', true)->count();
            return (int) round(($completed / $total) * 100);
        }

        return 0;
    }

    public function getIsCompleteAttribute(): bool
    {
        $total = $this->checklistItems()->count();
        if ($total === 0) return false;
        return $this->completedItems()->count() === $total;
    }
}
