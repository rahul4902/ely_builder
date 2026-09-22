<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use \App\Models\Concerns\BelongsToCompany;
    protected $fillable =
        [
            'name',
            'description', 'company_id'
        ];
}
