<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemberToConfirmOnList extends Model
{
    protected $table = 'memberstoconfirmonlist';

    protected $fillable = [
        'id', 'list_id', 'user_id',  
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
