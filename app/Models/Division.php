<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $table = 'division_pits'; // define division_pits table name
    protected $primaryKey = 'div_id'; // define div_id the primary key

    // Define relationship with Section
    public function sections()
    {
        return $this->hasMany(Section::class, 'div_id');
    }
}
