<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectMasterTableModel extends Model
{
    use \App\Models\Concerns\BelongsToCompany;
    protected $table = "project_master";
}
