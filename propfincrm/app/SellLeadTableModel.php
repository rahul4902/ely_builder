<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellLeadTableModel extends Model
{
    use \App\Models\Concerns\BelongsToCompany;
    protected $table = "sell_lead";
}
