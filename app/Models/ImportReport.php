<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportReport extends Model
{
    public $timestamps = true;

    protected $fillable =['report_id', 'duplicate_fields', 'duplicate_null', 'sum'];
}
