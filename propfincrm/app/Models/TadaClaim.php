<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Support\CurrentCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TadaClaim extends Model
{
    use BelongsToCompany;

    protected $table = 'tada_claims';

    protected $fillable = [
        'company_id',
        'user_id',
        'name',
        'dept',
        'date',
        'purpose',
        'allowance',
        'status',
    ];

    protected $casts = [
        'allowance' => 'float',
        'date' => 'date:Y-m-d',
    ];

    /**
     * Override bootBelongsToCompany to support both company-specific and unassigned fallback claims
     */
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $builder) {
            $context = app(CurrentCompany::class);
            $companyId = $context->id() 
                ?? (auth()->check() && auth()->user()->activeCompany() ? auth()->user()->activeCompany()->id : null);
            if ($companyId) {
                $table = $builder->getModel()->getTable();
                $builder->where(function ($q) use ($table, $companyId) {
                    $q->where($table . '.company_id', $companyId)
                      ->orWhereNull($table . '.company_id');
                });
            }
        });

        static::creating(function (Model $model) {
            $context = app(CurrentCompany::class);
            $companyId = $context->id() 
                ?? (auth()->check() && auth()->user()->activeCompany() ? auth()->user()->activeCompany()->id : null)
                ?? 14;
            if (!$model->getAttribute('company_id') && $companyId) {
                $model->setAttribute('company_id', $companyId);
            }
        });
    }
}
