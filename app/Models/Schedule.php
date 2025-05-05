<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'name',
        'start_hour',
        'end_hour',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
