<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemModel extends Model
{
    use HasFactory;

    protected $table = 'items';
    protected $fillable = ['id', 'id_app', 'id_class', 'id_instance', 'market_hash_name'];

    const CREATED_AT = null;
}
