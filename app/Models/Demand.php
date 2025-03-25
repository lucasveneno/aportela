<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demand extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    public function demandFiles()
    {
        return $this->hasMany(DemandFile::class);
    }

    protected $casts = [
        'area_id' => 'array',
        'files' => 'array',
    ];
}
