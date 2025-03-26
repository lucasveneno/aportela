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
        'location',
    ];
/**
     * The following code was generated for use with Filament Google Maps
     *
     * php artisan fgm:model-code Demand --lat=latitude --lng=longitude --location=location --terse
     */

     public function getLocationAttribute(): array
     {
         return [
             "lat" => (float)$this->latitude,
             "lng" => (float)$this->longitude,
         ];
     }
 
     public function setLocationAttribute(?array $location): void
     {
         if (is_array($location))
         {
             $this->attributes['latitude'] = $location['lat'];
             $this->attributes['longitude'] = $location['lng'];
             unset($this->attributes['location']);
         }
     }
 
     public static function getLatLngAttributes(): array
     {
         return [
             'lat' => 'latitude',
             'lng' => 'longitude',
         ];
     }
 
     public static function getComputedLocation(): string
     {
         return 'location';
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
        'location' => 'array',
        'files' => 'array',
    ];
}
