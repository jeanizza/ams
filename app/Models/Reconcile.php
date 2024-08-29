<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reconcile extends Model
{
    use HasFactory;

    protected $table = 'reconcile';
    protected $primaryKey = 'reconcile_id ';
    public $timestamps = false;

    protected $fillable = [
        'property_number',
        'remarks_reconcile',
        'po_number',
        'date_acquired',
        'amount',
        'user', // Add user column
        'date_created' // Add date_created column
    ];

}
