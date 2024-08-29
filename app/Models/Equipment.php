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
        'property_type', 'property_number', 'category', 'status', 'particular', 'description', 'brand', 'model', 'serial_no',
        'amount', 'date_acquired', 'po_number', 'end_user', 'position', 'office','division', 'section',
        'actual_user', 'position_actual_user', 'remarks', 'fund', 'lifespan', 'date_end', 'upload_image', 'uploaded_by'
    ];

    protected $casts = [
        'amount' => 'float', // cast the amount field to float
    ];

    // Relationship to Division
    public function division()
    {
        return $this->belongsTo(Division::class, 'div_id');
    }

    // Relationship to Section
    public function section()
    {
        return $this->belongsTo(Section::class, 'sec_id');
    }

   // Relationship to User (uploaded_by)
   public function uploadedBy()
   {
       return $this->belongsTo(User::class, 'name', 'uploaded_by');
   }
}
