<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddRecord extends Model
{
    use HasFactory;

    protected $table = 'add_record'; // define the add_record the table name
    protected $primaryKey = 'add_record_id';

    protected $fillable = [
        'property_type', 'property_number', 'category', 'particular', 'description', 'brand', 'model', 'serial_no', 'amount', 'date_acquired', 'po_number', 'end_user', 'position', 'office', 'div_name', 'sec_name', 'actual_user', 'position_actual_user', 'remarks', 'fund', 'lifespan', 'date_renewed', 'upload_image', 'uploaded_by'
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
