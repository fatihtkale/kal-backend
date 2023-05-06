<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Public_tasks extends Model
{
    use HasFactory;
    protected $table = 'public_tasks';
    protected $guarded = [];
    protected $hidden = [
        'updated_at', 'id', 'task_id', 'is_online'
    ];
    public $timestamps = true;

    public function task() {
        return $this->hasOne('App\Task', 'task_id');
    }
}
