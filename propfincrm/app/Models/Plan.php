<?php

namespace App\Models;

use App\Models\Concerns\UsesCentralConnection;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use UsesCentralConnection;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_cycle',
        'max_users',
        'features',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_users' => 'integer',
        'is_active' => 'boolean',
    ];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(CompanySubscription::class);
    }

    public function formattedPrice(): string
    {
        if ((float) $this->price <= 0) {
            return 'Free';
        }

        return '₹' . number_format($this->price, 2) . ' / ' . ucfirst($this->billing_cycle);
    }
}
