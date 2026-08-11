<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPoliceDeclaration extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'commissariat_id',
        'declared_by_user_id',
        'declaration_number',
        'receipt_photo',
        'declared_at',
    ];

    protected $casts = [
        'declared_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function commissariat()
    {
        return $this->belongsTo(Commissariat::class);
    }

    public function declaredBy()
    {
        return $this->belongsTo(User::class, 'declared_by_user_id');
    }
}
