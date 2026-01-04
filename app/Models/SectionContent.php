<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionContent extends Model
{
    protected $fillable = [
        'section_id',
        'key',
        'value',
        'order',
        'is_active'
    ];

    protected $casts = [
        'value' => 'array', // if JSON
    ];

    public function section()
    {
        return $this->belongsTo(PageSection::class, 'section_id');
    }
}
