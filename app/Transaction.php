<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

    ];
    // Get merchant of these transactions
    public function merchant()
    {
        return $this->belongsTo('App\User', 'merchant_id');
    }
    // Get acquirer of these transactions
    public function acquirer()
    {
        return $this->belongsTo('App\Acquirer', 'acquirer_id');
    }
    // Get acquirer of these transactions
    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id');
    }
}
