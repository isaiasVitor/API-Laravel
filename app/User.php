<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Address;
use App\CreateList;
use App\File;
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'name','cpf','rg','gender','available','birthday','api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',   
    ];

    public function file()
    {
        return $this->hasOne(File::class,'user_id');
    }

    public function address()
    {
        return $this->hasOne(Address::class,'user_id');
    }

    public function createList()
    {
        return $this->hasOne(CreateList::class,'user_id');
    }

    public function memberOfList()
    {
        return $this->hasOne(MemberOfList::class,'user_id');
    }

    public function memberToConfirmOnList()
    {
        return $this->hasOne(MemberToConfirmOnList::class,'user_id');
    }
}
