<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandFile extends Model
{
    use HasFactory;

    public function demand()
    {
        return $this->belongsTo(Demand::class);
    }
}
