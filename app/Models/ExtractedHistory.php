<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtractedHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'parent',
        'kid_id',
        'kid_name',
        'level',
        'date',
        'subject_id',
        'subject',
        'type',
        'unit',
        'section',
        'duration',
        'score',
        'link',
    ];
}
