<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $table = 'transfer_data';
    protected $primaryKey = 'transfer_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'transfer_id', 'property_number', 'end_user', 'position', 'transfer_office',
        'transfer_division', 'transfer_enduser', 'transfer_position', 'transfer_condition',
        'reason_transfer', 'date_transfer'
    ];

    public $timestamps = false;
}
