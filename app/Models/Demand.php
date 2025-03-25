<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demand extends Model
{
    use HasFactory;
    
    protected $table = 'demand_files'; // Ensure this points to the correct table


    public function demandFiles()
    {
        return $this->hasMany(DemandFile::class);
    }

    

    protected $casts = [
        'area_id' => 'array',
    ];
}
