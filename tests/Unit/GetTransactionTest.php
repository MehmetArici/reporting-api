<?php

namespace Tests\Unit;

use App\Transaction;
use App\User;
use Tests\TestCase;
use JWTAuth;
use Illuminate\Http\Response as HttpResponse;

class GetTransactionTest extends TestCase
{
    /**
     * A test in transaction with requested transactionId.
     *
     * @return void
     */
    public function test_response_transaction_in_requested_transactionId()
    {
        // get the response with request and header Auth token of first user
        $response = $this->post(
            'api/v3/transaction',
            ['transactionId' =>'1-1444392440-1'],
            $this->headers(User::first())
        );
        $this->assertEquals(HttpResponse::HTTP_OK, $response->status());
        // get transaction of transaction from DB
        $transaction = Transaction::where('transactionId', '1-1444392440-1')->first();
        $this->assertNotNull($transaction);
        $transactionResource = new \App\Http\Resources\Transaction($transaction, $transaction);
        // Check response data and collection of data from DB
        $this->assertEquals($transactionResource->transactionId, $response->getData()->transaction->merchant->transactionId);
    }
    public function test_response_transaction_in_invalid_requested_transactionID()
    {
        // get the response with transactionID and header Auth token of first user
        $response = $this->post('api/v3/transaction', ['transactionId' => '1-1'], $this->headers(User::first()));
        $this->assertEquals(HttpResponse::HTTP_OK, $response->status());
        // get client of transaction from DB
        $transaction = Transaction::where('transactionId', '1-1')->first();
        $this->assertNull($transaction);

    }
    public function test_response_transaction_in_none_requested_transactionID()
    {
        // get the response with transactionID and header Auth token of first user
        $response = $this->post('api/v3/transaction');
        $this->assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->status());
    }
    public function test_response_transaction_in_none_requested_transactionID_with_header_token()
    {
        // get the response with transactionID and header Auth token of first user
        $response = $this->post('api/v3/transaction', $this->headers(User::first()));
        $this->assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->status());
    }
    public function test_response_transaction_in_requested_transactionID_with_none_header_token()
    {
        // get the response with transactionID and header Auth token of first user
        $response = $this->post('api/v3/transaction', ['transactionId' => '1-1444392440-1']);
        $this->assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->status());
    }
    public function test_response_transaction_in_requested_transactionID_with_none_user_header_token()
    {
        // get the response with transactionID and header Auth token of first user
        $response = $this->post('api/v3/transaction', ['transactionId' => '1-1444392440-1'], $this->headers(User::find(100)));
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
