<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintDefect extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'complaints_defects';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'property_number',
        'type_of_equipment',
        'serial_no',
        'division',
        'complaints',
        'defects',
        'parts_to_be_repaired',
        'remarks',
    ];
}
