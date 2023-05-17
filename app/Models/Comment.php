<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';

    protected $guarded = [''];

    protected $appends = ['author'];

    public function getAuthorAttribute() {
        return $this->belongsTo('App\Models\User', 'user_id')->select('name')->first();
    }
}
