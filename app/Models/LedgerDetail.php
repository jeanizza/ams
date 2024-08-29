<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerDetail extends Model
{
    use HasFactory;

    use HasFactory;

    protected $fillable = [
        'property_number', 'po_number', 'date_created', 'quantity', 'unit', 'supplier', 'defects', 'particular', 'unit_cost', 'total_amount', 'remarks'
    ];
}
