<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BudgetMasterTableModel extends Model
{
    use \App\Models\Concerns\BelongsToCompany;
    protected $table = "budget_master";
}
