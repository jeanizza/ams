<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $table = 'section'; // define the section table name
    protected $primaryKey = 'sec_id'; // define the sec_id the primary key

    // Define relationship with Division
    public function division()
    {
        return $this->belongsTo(Division::class, 'div_id');
    }
}
