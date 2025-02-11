<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceLedger extends Model
{
    use HasFactory;

    protected $table = 'maintenance_ledger';
    protected $primaryKey = 'maintenance_ledger_id';
    public $incrementing = false; // Disable auto-incrementing
    public $timestamps = false; // Disable timestamps
    protected $keyType = 'string';

    protected $fillable = [
        'maintenance_ledger_id', 'equipment_id', 'property_number', 'date_delivered','quantity','unit', 'particular_ledger', 'description_ledger', 
        'defects', 'po_number', 'supplier', 'unit_cost', 'total_amount', 'remarks','date_created', 'date_updated'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate custom ID for primary key
            $latestEntry = self::latest('maintenance_ledger_id')->first();
            $nextId = $latestEntry ? ((int) str_replace('ML-', '', $latestEntry->maintenance_ledger_id)) + 1 : 1;
            $model->maintenance_ledger_id = 'ML-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        });
    }
}
