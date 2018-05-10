<?php

namespace App\Http\Controllers;

use App\Acquirer;
use App\Agent;
use App\Client;
use App\Transaction;
use App\User;
use App\Http\Resources\Transaction as TransactionResource;
use Request;

class TransactionController extends Controller
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
     * Report the transaction of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function report()
    {
        // Get Merchant of the requested transaction
        $user = User::find(request('merchant'));
        // Check is there any Merchant for requested ID
        if (empty($user)){
            return response()->json([
                'status'    => 'DECLINED',
                'message'   => 'Error : No Merchant found for given ID'
            ]);
        }
        // Get transaction of given request of requested merchant
        $transaction = $user->transactions()->where([
            'fromDate'      => request('fromDate'),
            'toDate'        => request('toDate'),
            'acquirer_id'   => request('acquirer')
        ])->get();
        // Selecting attribute of response transaction
        $attribute = [];
        foreach ($transaction as $t){
            $temp = [
                'count' => $t->count,
                'total' => $t->total,
                'currency' => $t->currency
            ];
            //push the each transaction info to attribute array
            array_push($attribute, $temp);
        }
        return response()->json([
            'status'   => empty($transaction[0]) ? 'DECLINED' : 'APPROVED',
            'response' => empty($transaction[0]) ? 'Error : No Transaction found for given request' : new TransactionResource($transaction, $attribute)
        ]);
    }

    /**
     * Show the list of the requested resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        // Get the Transactions according to requested array
        $transactions = $this->filterTransaction(request())->toArray();
        $attribute = [
            'per_page'      => $transactions['per_page'],
            'current_page'  => $transactions['current_page'],
            'next_page_url' => $transactions['next_page_url'],
            'prev_page_url' => $transactions['prev_page_url'],
            'from'          => $transactions['from'],
            'to'            => $transactions['to'],
            'data' => [
            ]
        ];
        // If there is a request, then set the data array into attribute
        if (count(request()->all())) {
            foreach ($transactions['data'] as $transaction){
                $objectTransaction = (object) $transaction;
                $merchant = User::find($objectTransaction->merchant_id);
                $client = Client::find($objectTransaction->client_id);
                $acquirer = Acquirer::find($objectTransaction->acquirer_id);

                $temp = [
                    'fx' => [
                        'merchant' => [
                            'originalAmount' => $merchant->originalAmount,
                            'originalCurrency' => $merchant->originalCurrency
                        ]
                    ],
                    'customerInfo' => [
                        'number'            => $client->number,
                        'email'             => $client->email,
                        'billingFirstName'  => $client->billingFirstName,
                        'billingLastName'   => $client->billingLastName
                    ],
                    'merchant' => [
                        'id'    => $merchant->id,
                        'name'  => $merchant->name
                    ],
                    'ipn' => ['received' => $objectTransaction->isIpn ? true : false],
                    'transaction' => [
                        'merchant' => [
                            'referenceNo'   => $objectTransaction->referenceNo,
                            'status'        => $objectTransaction->status,
                            'operation'     => $objectTransaction->operation,
                            'message'       => $objectTransaction->operation,
                            'created_at'    => $objectTransaction->created_at,
                            'transactionId' => $objectTransaction->transactionId
                        ]
                    ],
                    'acquirer' => [
                        'id'    => $acquirer->id,
                        'name'  => $acquirer->name,
                        'code'  => $acquirer->code,
                        'type'  => $acquirer->type
                    ],
                    'refundable' => $objectTransaction->isRefundable ? true : false
                ];
                array_push($attribute['data'], $temp);
            }
        }
        return new TransactionResource($transactions, $attribute);
    }

    /**
     * Filter transaction for given requests
     *
     */
    private function filterTransaction($request){
        // If request has fromData and toDate attr.
        if ($request->has('fromDate') and $request->has('toDate')){
            $where = [
                'fromDate' => $request->fromDate,
                'toDate'   => $request->toDate
            ];
            // If request has merchant and acquirer
            if ($request->has('merchant') and $request->has('acquirer')){
                $temp = [
                    'merchant_id'   => $request->merchant,
                    'acquirer_id'   => $request->acquirer
                ];
                $where = array_merge($where, $temp);
                if ($request->has('status') and $request->has('operation') and $request->has('paymentMethod') and $request->has('filterField') and $request->has('filterValue') and $request->has('page')){
                    $temp2 = [
                        'status'                => $request->status,
                        'operation'             => $request->operation,
                        'paymentMethod'         => $request->paymentMethod,
                        $request->filterField   => $request->filterValue,
                    ];
                    $where = array_merge($where, $temp2);
                }
            }
            // return Transaction response with given request via pagination in a specific page
            return Transaction::where($where)->paginate(50, ['*'], 'page', $request->page );
        }
        else if($request->has('status') and $request->has('operation') and $request->has('errorCode')){
            $where = [
                'status'       => $request->status,
                'operation'    => $request->operation,
                'errorCode'    => $request->errorCode
            ];
            return Transaction::where($where)->paginate(50);
        }
        else{
            // if there is no request then response only pagination info
            return Transaction::paginate(50);
        }
    }

    /**
     * Request for all information of transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function transaction()
    {
        // Get Transaction
        $transaction = Transaction::where('transactionId', request('transactionId'))->first();
        // Check is there any Transaction for requested ID
        if (empty($transaction)){
            return response()->json([
                'status'    => 'DECLINED',
                'message'   => 'Error : No Transaction found for requested ID'
            ]);
        }
        //Get Merchant, Agent and Client of above transaction
        $merchant = $transaction->merchant;
        $client = $transaction->client;
        $agent = Agent::where('id', $transaction->agentInfoId)->first();
        // set response parameter
        $attribute = [
            "fx" => [
                "merchant"  => [
                    "originalAmount"    => $merchant->originalAmount,
                    "originalCurrency"  => $merchant->originalCurrency
                ]
            ],
            "customerInfo" => $client,
            "merchant"  => [
                "name"  => $merchant->name
            ],
            "transaction" => [
                "merchant" => [
                    "referenceNo"           => $transaction->referenceNo,
                    "merchantId"            => $merchant->id,
                    "status"                => $transaction->status,
                    "channel"               => $transaction->channel,
                    "customData"            => $transaction->customData,
                    "chainId"               => $transaction->chainId,
                    "agentInfoId"           => $transaction->agentInfoId,
                    "operation"             => $transaction->operation,
                    "fxTransactionId"       => $transaction->fx_id,
                    "updated_at"            => $transaction->updated_at->toDateTimeString(),
                    "created_at"            => $transaction->created_at->toDateTimeString(),
                    "id"                    => $transaction->id,
                    "acquirerTransactionId" => $transaction->acquirer_id,
                    "code"                  => $transaction->code,
                    "message"               => $transaction->message,
                    "transactionId"         => $transaction->transactionId
                ],
                "agent" => [
                    "id"                => $agent->id,
                    "customerIp"        => $agent->customerIp,
                    "customerUserAgent" => $agent->customerUserAgent,
                    "merchantIp"        => $agent->merchantIp,
                ]
            ]
        ];
        //Return Transaction Resource response
        return new TransactionResource($transaction, $attribute);
    }
}
