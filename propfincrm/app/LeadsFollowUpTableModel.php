<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadsFollowUpTableModel extends Model
{
    use \App\Models\Concerns\BelongsToCompany;
    protected $table = "lead_follow_up";
}
