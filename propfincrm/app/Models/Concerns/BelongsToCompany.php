<?php

namespace App\Models\Concerns;

use App\Support\CurrentCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $builder) {
            $context = app(CurrentCompany::class);
            if ($context->id()) {
                $builder->where($builder->getModel()->getTable() . '.company_id', $context->id());
            }
        });

        static::creating(function (Model $model) {
            $context = app(CurrentCompany::class);
            if (!$model->getAttribute('company_id') && $context->id()) {
                $model->setAttribute('company_id', $context->id());
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
