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
        'property_type',
        'property_number',
        'reference_no',
        'category',
        'status',
        'office',
        'particular',
        'description',
        'serial_no',
        'model',
        'brand',
        'amount',
        'qty',
        'total_amount',
        'po_number',
        'date_acquired',
        'end_user',
        'position',
        'section',
        'division',
        'actual_user',
        'position_actual_user',
        'equipment_location',
        'remarks',
        'transferred_to',
        'fund',
        'lifespan',
        'date_end',
        'date_renewed',
        'date_entered',
        'officeOfActualUser',
        'officeOfEndUser',
        'upload_image',
        'uploaded_by',
        'updated_by',
        'date_updated',
        'request_id',
    ];

    protected $casts = [
        'amount' => 'float', // cast the amount field to float
        'lifespan' => 'integer',
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
