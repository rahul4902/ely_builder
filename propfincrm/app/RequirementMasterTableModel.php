<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequirementMasterTableModel extends Model
{
    use \App\Models\Concerns\BelongsToCompany;
    protected $table = "requirement_master";
}
