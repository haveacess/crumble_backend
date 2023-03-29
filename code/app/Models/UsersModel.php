<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersModel extends Model
{
    use HasFactory;

    protected $table = 'users';
    protected $fillable = ['id', 'id_steam64', 'login', 'country_code'];
    public $timestamps = false;
}
