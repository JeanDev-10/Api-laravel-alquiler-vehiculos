<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CloudinaryLabs\CloudinaryLaravel\MediaAlly;
class Vehiculo extends Model
{
    use HasFactory;
    use MediaAlly;
    protected $fillable=['modelo','marca',"public_id",
    "url"];
}
