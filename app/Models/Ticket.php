<?php

namespace App\Models;

use App\Enums\TicketType;
use App\Enums\TicketStatus;
use App\Enums\TicketPriority;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'client_id',
        'type',
        'title',
        'description',
        'format',
        'priority',
        'status',
        'assigned_to',
        'created_by',
        'deadline',
        'requires_client_approval',
        'client_approved',
        'client_approved_at',
        'delivered_at',
        'kanban_order',
        'history_field',
        'history_old_value',
        'history_new_value',
        'history_user_id',
        'history_updated_at',
    ];

    protected $casts = [
        'type' => TicketType::class,
        'status' => TicketStatus::class,
        'priority' => TicketPriority::class,
        'deadline' => 'date',
        'requires_client_approval' => 'boolean',
        'client_approved' => 'boolean',
        'client_approved_at' => 'datetime',
        'delivered_at' => 'datetime',
        'history_updated_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class)->orderBy('created_at');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function historyUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'history_user_id');
    }

    /**
     * Record a status or property change directly into the ticket columns.
     */
    public function recordHistory(?string $field, ?string $oldValue, ?string $newValue, ?int $userId = null): void
    {
        $this->update([
            'history_field' => $field,
            'history_old_value' => $oldValue,
            'history_new_value' => $newValue,
            'history_user_id' => $userId ?? auth()->id(),
            'history_updated_at' => now(),
        ]);
    }

    /**
     * Backward-compatible accessor for history log collection.
     */
    public function getHistoryAttribute()
    {
        if (empty($this->history_new_value)) {
            return collect();
        }

        return collect([(object)[
            'field' => $this->history_field,
            'old_value' => $this->history_old_value,
            'new_value' => $this->history_new_value,
            'user' => $this->historyUser,
            'created_at' => $this->history_updated_at ?: $this->updated_at,
        ]]);
    }

    public function qaReviews(): HasMany
    {
        return $this->hasMany(QaReview::class);
    }

    public function latestApprovedQa()
    {
        return $this->hasOne(QaReview::class)->where('is_approved', true)->latestOfMany();
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline && $this->deadline->isPast() && $this->status !== TicketStatus::Delivered;
    }

    public function getHasApprovedQaAttribute(): bool
    {
        return $this->qaReviews()->where('is_approved', true)->exists();
    }

    /**
     * Generate next ticket number (SF-0001, SF-0002, etc.)
     */
    public static function generateTicketNumber(): string
    {
        $last = static::withTrashed()->orderByDesc('id')->first();
        $nextNumber = $last ? ((int) substr($last->ticket_number, 3)) + 1 : 1;
        return 'SF-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
