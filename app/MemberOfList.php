<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemberOfList extends Model
{
    protected $table = 'membersoflist';

    protected $fillable = [
        'id', 'userConfirm','ownerConfirm', 'list_id', 'user_id',  
    ];

    
    public function createList()
    {
        return $this->belongsTo(CreateList::class,'user_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

}
