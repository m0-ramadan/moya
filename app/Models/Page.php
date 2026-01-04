<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['key', 'title', 'is_active'];

    public function sections()
    {
        return $this->hasMany(PageSection::class);
    }
}
