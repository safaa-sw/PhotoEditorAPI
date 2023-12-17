<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoEditing extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'name','path','type','data','output_path','user_id',
    ];
}
