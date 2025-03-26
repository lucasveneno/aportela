<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demand extends Model
{
    use HasFactory;


    /**
     *
     * Replace your existing $fillable and/or $guarded and/or $appends arrays with these - we already merged
     * any existing attributes from your model, and only included the one(s) that need changing.
     */


     protected $fillable = [
        'user_id',
        'zip',
        'address',
        'city',
    ];


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
