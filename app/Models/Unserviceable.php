<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unserviceable extends Model
{
    use HasFactory;

    // Specify the table name if it's not the plural of the model name
    protected $table = 'unserviceable';
    public $timestamps = false;


    // Specify the fields that are mass assignable
    protected $fillable = [
        'property_number',
        'property_type',
        'item_description',
        'unit_price',
        'date_acquired',
        'quantity',
        'remarks',
        'returned_by',
        'status',
    ];
}
