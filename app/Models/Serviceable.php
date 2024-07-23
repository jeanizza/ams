<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Serviceable extends Model
{
    use HasFactory;

    protected $table = 'serviceable';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

}
