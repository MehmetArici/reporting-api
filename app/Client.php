<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

    ];

    // Get transaction of this client
    public function transactions()
    {
        return $this->hasMany('App\Transaction', 'client_id');
    }
}
