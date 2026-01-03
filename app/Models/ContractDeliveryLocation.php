<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractDeliveryLocation extends Model
{
    protected $fillable = [
        'contract_id',
        'saved_location_id',
        'priority',
        'notes'
    ];

    // علاقة الموقع مع العقد
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    // علاقة الموقع مع الموقع المحفوظ
    public function savedLocation(): BelongsTo
    {
        return $this->belongsTo(SavedLocation::class);
    }
}
