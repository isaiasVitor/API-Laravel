<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreateList extends Model
{
    protected $table = 'createlist';

    protected $fillable = [
        'id', 'name', 'origin','destination','date','schedules','conductor', 'user_id',  
    ];



    protected $touches = ['user'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
