<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Heros extends Model
{
    use HasFactory;
    protected $table = 'heros';
    protected $fillable = [
        'contents',
        'header_text',
        'description',
        'hero_key',
        'heroimage',
    ];
}
