<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class whyChooseUsModel extends Model
{
    use HasFactory;
    protected $table = 'why_choose_us';
    protected $fillable= [
        'reason',
        'image',
    ];
}
