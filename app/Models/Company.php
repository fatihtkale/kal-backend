<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'company';
    protected $guarded = [''];

    public function tasks() {
        return $this->hasMany('App\Models\Task');
    }

    protected $hidden = [
        'is_activated',
        'updated_at',
    ];

    use HasFactory;
}
