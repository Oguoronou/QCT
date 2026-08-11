<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commissariat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'commune',
        'city',
        'phone',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function declarations()
    {
        return $this->hasMany(ItemPoliceDeclaration::class);
    }
}
