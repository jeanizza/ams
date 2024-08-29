<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobRequest extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'type',
        'date_time_requested',
        'name',
        'division',
        'type_of_request',
        'specify',
        'job_description',
    ];

}
