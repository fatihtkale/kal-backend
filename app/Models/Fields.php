<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fields extends Model
{
    use HasFactory;

    protected $table = 'fields';
    protected $guarded = [];
    public $timestamps = true;
    protected $casts = [
        'fields' => 'array'
    ];

    protected $hidden = [
        'is_deleted',
        'created_at',
        'updated_at',
    ];
}
