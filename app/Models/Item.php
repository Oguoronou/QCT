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
        'images',  // Utilisé dans le code
        'description',
        'status',
        'lost_found_status',
    ];

    /**
     * Les statuts possibles
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec le propriétaire de l'objet
     */
    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    /**
     * Relation avec l'utilisateur qui a trouvé l'objet
     */
    public function foundUser()
    {
        return $this->belongsTo(User::class, 'found_user_id');
    }

    /**
     * Déclaration de dépôt au commissariat, le cas échéant
     */
    public function policeDeclaration()
    {
        return $this->hasOne(ItemPoliceDeclaration::class);
    }

    /**
     * Obtenir le tableau des images
     */
    public function getImagesArray()
    {
        if (!$this->images) {
            return [];
        }
        return explode(',', $this->images);
    }

    /**
     * Obtenir la première image
     */
    public function getFirstImage()
    {
        $images = $this->getImagesArray();
        return $images[0] ?? asset('placeholder.jpg');
    }
}

