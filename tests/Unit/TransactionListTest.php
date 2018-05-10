<?php

namespace Tests\Unit;

use App\Transaction;
use App\User;
use Tests\TestCase;
use JWTAuth;
use Illuminate\Http\Response as HttpResponse;

class TransactionListTest extends TestCase
{
    /**
     * A test in transaction with requested parameters.
     *
     * @return void
     */
    public function test_response_list_in_none_requested_parameters()
    {
        // get the response with request and header Auth token of first user
        $response = $this->post(
            'api/v3/transaction/list',
            [],
            $this->headers(User::first())
        );
        $this->assertEquals(HttpResponse::HTTP_OK, $response->status());
        // get transaction of transaction from DB
        $transaction = Transaction::paginate(50)->toArray();
        $this->assertNotNull($transaction);
        // Check response data and collection of data from DB
        $this->assertEmpty($response->getData()->data);
        $this->assertEquals($transaction['current_page'], $response->getData()->current_page);
    }
    public function test_response_list_in_requested_parameters()
    {
        // get the response with request and header Auth token of first user
        $response = $this->post(
            'api/v3/transaction/list',
            [
                'fromDate'      => '2015-07-01',
                'toDate'        => '2015-10-01',
                'merchant'      => 1,
                'acquirer'      => 1,
                'status'        => 'APPROVED',
                'operation'     => '3DAUTH',
                'paymentMethod' => 'CREDITCARD',
                'filterField'   => 'referenceNo',
                'filterValue'   => 'api_560a4a9314208',
                'page'          => 1
            ],
            $this->headers(User::first())
        );
        $this->assertEquals(HttpResponse::HTTP_OK, $response->status());
        // get transaction of transaction from DB
        $transaction = Transaction::where([
            'fromDate'      => '2015-07-01',
            'toDate'        => '2015-10-01',
            'merchant_id'   => 1,
            'acquirer_id'   => 1,
            'status'        => 'APPROVED',
            'operation'     => '3DAUTH',
            'paymentMethod' => 'CREDITCARD',
            'referenceNo'   => 'api_560a4a9314208'
        ])->paginate(50, ['*'], 'page', 1);
        $this->assertNotNull($transaction);
        $resourceTransaction = new \App\Http\Resources\Transaction($transaction, $transaction);
        // Check response data and collection of data from DB
        for($i=0; $i < count($response->getData()->data); $i++){
            $this->assertEquals($response->getData()->data[$i]->transaction->merchant->transactionId, $resourceTransaction[$i]->transactionId);
        }
    }

    public function test_response_list_in_invalid_requested_parameters()
    {
        $response = $this->post(
            'api/v3/transaction/list',
            [
                'fromDate'      => '2015-07-01',
                'toDate'        => '2015-10-01',
                'merchant'      => 1,
                'acquirer'      => 1,
            ],
            $this->headers(User::first())
        );
        $this->assertEquals(HttpResponse::HTTP_OK, $response->status());
        // get transaction of transaction from DB
        $transaction = Transaction::where([
            'fromDate'      => '2020-07-01',
            'toDate'        => '2020-10-01',
            'merchant_id'   => 3,
            'acquirer_id'   => 98
        ])->paginate(50);
        $this->assertNull($transaction['items']);
    }

    public function test_response_list_in_none_requested_parameters_and_token()
    {
        $response = $this->post('api/v3/transaction/list');
        $this->assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->status());
    }

    public function test_response_list_in_requested_params_with_none_header_token()
    {
        $response = $this->post(
            'api/v3/transaction/list',
            [
                'fromDate'      => '2015-07-01',
                'toDate'        => '2015-10-01',
                'merchant'      => 1,
                'acquirer'      => 1,
            ]
        );
        $this->assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->status());
    }
    public function test_response_list_in_requested_params_with_none_user_header_token()
    {
        // get the response with transactionID and header Auth token of first user
        $response = $this->post(
            'api/v3/transaction/list',
            [
                'fromDate'      => '2015-07-01',
                'toDate'        => '2015-10-01',
                'merchant'      => 1,
                'acquirer'      => 1,
            ],
            $this->headers(User::find(100))
        );
        $this->assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->status());
    }

    protected function headers($user = null)
    {
        $headers = ['Accept' => 'application/json'];

        if (!is_null($user)) {
            $token = JWTAuth::fromUser($user);
            JWTAuth::setToken($token);
            $headers['Authorization'] = 'Bearer '.$token;
        }

        return $headers;
    }
}
