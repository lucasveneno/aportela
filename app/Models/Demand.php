<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demand extends Model
{
    use HasFactory;


    /**
     * The following code was generated for use with Filament Google Maps
     *
     * php artisan fgm:model-code Demand --lat=latitude --lng=longitude --location=formatted_address --terse
     */


    protected $fillable = [
        'user_id',
        'formatted_address',
    ];

    protected $appends = [
        'location',
        'formatted_address',
    ];

    public function getFormattedAddressAttribute(): array
    {
        return [
            "lat" => (float)$this->latitude,
            "lng" => (float)$this->longitude,
        ];
    }

    public function setFormattedAddressAttribute(?array $location): void
    {
        if (is_array($location)) {
            $this->attributes['latitude'] = $location['lat'];
            $this->attributes['longitude'] = $location['lng'];
            unset($this->attributes['formatted_address']);
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
        return 'formatted_address';
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
        //'location' => 'array',
        'files' => 'array',
    ];
}
