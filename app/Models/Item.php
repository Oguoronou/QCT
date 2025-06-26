<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = "items";

    protected $fillable = [
        'user_id',
        'found_user_id',
        'item_name',
        'category_name',
        'date',
        'images',
        'description',
        'status',
        'lost_found_status',
    ];

    public function users()
    {
        return $this->belongsTo("App\Models\User", "user_id", "id");
    }
}
