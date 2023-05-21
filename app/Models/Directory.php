<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    use HasFactory;

    protected $table = 'directory';
    protected $guarded = [];
    public $timestamps = true;
    protected $casts = [
        'data' => 'array',
        'fields' => 'array'
    ];

    protected $hidden = [
        'is_deleted',
        'created_at',
        'updated_at',
    ];

    /** 
     *  FOR GETTING NON SOFT DELETED DATA
     * 
     *  protected $appends = ['nonDeletedData'];
     *
     *  public function getNonDeletedDataAttribute($value) {
     *      return array_filter($this->data, function ($data) {
     *          return !isset($data['is_deleted']);
     *      });
     *  }
     *
     */
}
