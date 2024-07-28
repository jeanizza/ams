<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment';
    protected $primaryKey = 'equipment_id';
    public $timestamps = false;

    protected $fillable = [
        'property_number', 'category', 'particular', 'description', 'brand', 'model', 'serial_no',
        'amount', 'date_acquired', 'po_number', 'end_user', 'position', 'division', 'section',
        'actual_user', 'remarks', 'fund', 'lifespan', 'date_renewed', 'upload_image'
    ];
}
