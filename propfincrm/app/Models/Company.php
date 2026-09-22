<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use \App\Models\Concerns\UsesCentralConnection;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'is_active',
        'plan_id',
        'plan_expires_at',
        'subscription_status',
        'max_users',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'plan_expires_at' => 'datetime',
        'max_users' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($company) {
            if (empty($company->tenant_id)) {
                $company->tenant_id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user')
            ->withPivot(['role', 'teamlead_user_id', 'is_active'])
            ->withTimestamps();
    }

    public function activeUsers()
    {
        return $this->users()->wherePivot('is_active', true);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function settings()
    {
        // Platform administration reads tenant settings across company scopes.
        return $this->hasOne(Setting::class)->withoutGlobalScopes();
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(CompanySubscription::class)->orderBy('created_at', 'desc');
    }

    public function payments()
    {
        return $this->hasMany(CompanyPayment::class)->orderBy('payment_date', 'desc');
    }

    public function tickets()
    {
        return $this->hasMany(SupportTicket::class)->orderBy('created_at', 'desc');
    }

    public function isExpired(): bool
    {
        return $this->plan_expires_at && $this->plan_expires_at->isPast();
    }

    public function daysRemaining(): ?int
    {
        if (!$this->plan_expires_at) {
            return null;
        }

        return (int) now()->diffInDays($this->plan_expires_at, false);
    }

    public function planName(): string
    {
        return $this->plan?->name ?? 'None';
    }

}
