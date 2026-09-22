<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class CompanyIntegration extends Model
{
    use BelongsToCompany, \App\Models\Concerns\UsesCentralConnection;

    protected $fillable = ['company_id', 'source_id', 'provider', 'name', 'credentials', 'configuration', 'is_active', 'sync_frequency', 'last_synced_at', 'last_error'];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'configuration' => 'encrypted:array',
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    public function source()
    {
        return $this->belongsTo(\App\SourceMasterTableModel::class, 'source_id');
    }
}
