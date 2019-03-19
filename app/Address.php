<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'address';

    protected $fillable = [
        'zipcode', 'country', 'state', 'city', 'neighborhood', 'street', 'number', 'complement','phone_first','phone_secondary'
    ];
  
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
