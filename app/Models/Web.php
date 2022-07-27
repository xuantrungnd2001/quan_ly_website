<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Web extends Model
{
    protected $fillable = [
        'id', 'url', 'status', 'http_code', 'title', 'owner',  'created_at', 'updated_at', 'last_check'
    ];
    use HasFactory;
}