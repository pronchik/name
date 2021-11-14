<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'category_id',
        'seller_user_id',
        'owner_user_id',
        'status_id'
    ];

    protected $hidden = [
        'updated_at',
        'created_at'
    ];
}
