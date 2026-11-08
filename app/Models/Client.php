<?php

namespace App\Models;

use App\Enums\ClientLevel;
use App\Enums\ClientStatus;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'status',
        'level',
        'ads_budget',
        'account_manager_id',
        'start_date',
        'renewal_date',
        'has_sla',
        'main_platform',
        'main_objective',
        'logo',
        'notes',
        'access_platform',
        'access_url',
        'access_username',
        'access_password',
        'access_notes',
    ];

    protected $casts = [
        'status' => ClientStatus::class,
        'level' => ClientLevel::class,
        'ads_budget' => 'decimal:2',
        'start_date' => 'date',
        'renewal_date' => 'date',
        'has_sla' => 'boolean',
    ];

    /**
     * Resilient access password attribute:
     * - Encrypts on set
     * - Safely decrypts on get; if stored as plain text, returns it without crashing
     */
    protected function accessPassword(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                if (empty($value)) {
                    return null;
                }
                try {
                    return Crypt::decryptString($value);
                } catch (DecryptException) {
                    return $value;
                }
            },
            set: function (?string $value) {
                if (empty($value)) {
                    return null;
                }
                try {
                    Crypt::decryptString($value);
                    return $value;
                } catch (DecryptException) {
                    return Crypt::encryptString($value);
                }
            }
        );
    }

    public function accountManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ClientContact::class);
    }

    /**
     * Backward-compatible accessor for accesses collection.
     */
    public function getAccessesAttribute()
    {
        if (empty($this->access_platform)) {
            return collect();
        }

        return collect([(object)[
            'id' => $this->id,
            'platform' => $this->access_platform,
            'url' => $this->access_url,
            'username' => $this->access_username,
            'password' => $this->access_password,
            'notes' => $this->access_notes,
        ]]);
    }

    public function getHasAccessAttribute(): bool
    {
        return !empty($this->access_platform);
    }

    public function onboarding(): HasOne
    {
        return $this->hasOne(Onboarding::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function primaryContact(): HasOne
    {
        return $this->hasOne(ClientContact::class)->where('is_primary', true);
    }
}
