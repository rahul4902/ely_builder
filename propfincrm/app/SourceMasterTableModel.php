<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SourceMasterTableModel extends Model
{
    use \App\Models\Concerns\BelongsToCompany;
    protected $table = "source_master";
}
