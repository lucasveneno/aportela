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

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            // Automatically set the user_id to the currently authenticated user
            if (auth()->check()) {
                $model->user_id = auth()->id();  // Sets the authenticated user's ID
            }
        });
    }

    protected $casts = [
        'area_id' => 'array',
        'files' => 'array',
    ];
}
