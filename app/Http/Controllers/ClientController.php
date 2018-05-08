<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\Http\Resources\Client as ClientResource;

class ClientController extends Controller
{
    /**
     * Check the token instance is valid.
     * Except the Login
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['login']]);
    }

    /**
     * Request for information of client.
     *
     * @return \Illuminate\Http\Response
     */
    public function clientInfo()
    {
        // Get the client of requested Transaction
        $transaction = Transaction::where('transactionId', request('transactionId'))->first();
        if (empty($transaction)){
            return response()->json([
                'status'    => 'DECLINED',
                'message'   => 'Error : No Transaction found for given ID'
            ]);
        }
        // Set the response json attribute
        $attribute = [
            'customerInfo' => $transaction->client
        ];
        return ['customerInfo' => new ClientResource($transaction->client, $attribute)];
    }
}
