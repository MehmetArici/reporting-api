<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Acquirer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

    ];

    // Get transaction of this acquirer
    public function transactions()
    {
        return $this->hasMany('App\Transaction', 'acquirer_id');
    }
}
