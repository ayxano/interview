<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

/**
 * @method static findOrFail(string $id)
 */
class Vehicle extends Model
{
    protected $table = 'vehicleTypes';
}
