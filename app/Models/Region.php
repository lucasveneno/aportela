<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

class Region extends Model
{
    use HasFactory;

    public static function schema(Blueprint $table)
    {
        $table->string('name');
    }
}
