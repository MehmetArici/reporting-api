<?php

namespace Tests\Unit;

use App\Http\Resources\Client;
use App\Transaction;
use App\User;
use Tests\TestCase;
use JWTAuth;
use Illuminate\Http\Response as HttpResponse;


class ClientTest extends TestCase
{
    /**
     * A test in client with requested transaction.
     *
     * @return void
     */
    public function test_response_client_in_requested_transaction()
    {
        // get the response with transactionID and header Auth token of first user
        $response = $this->post('api/v3/client', ['transactionId' => '1-1444392440-1'], $this->headers(User::first()));
        $this->assertEquals(HttpResponse::HTTP_OK, $response->status());
        // get client of transaction from DB
        $client = Transaction::where('transactionId', '1-1444392440-1')->first()->client;
        $this->assertNotNull($client);
        $clientResource = new Client($client);
        // checking ID of these types enough because they are primary
        $this->assertEquals($clientResource->id, $response->getData()->customerInfo->id);
    }
    public function test_response_client_in_invalid_requested_transaction()
    {
        // get the response with transactionID and header Auth token of first user
        $response = $this->post('api/v3/client', ['transactionId' => '1-1'], $this->headers(User::first()));
        $this->assertEquals(HttpResponse::HTTP_OK, $response->status());
        // get client of transaction from DB
        $transaction = Transaction::where('transactionId', '1-1')->first();
        $this->assertNull($transaction);

    }
    public function test_response_client_in_none_requested_transaction()
    {
        // get the response with transactionID and header Auth token of first user
        $response = $this->post('api/v3/client');
        $this->assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->status());
    }
    public function test_response_client_in_none_requested_transaction_with_header_token()
    {
        // get the response with transactionID and header Auth token of first user
        $response = $this->post('api/v3/client', $this->headers(User::first()));
        $this->assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->status());
    }
    public function test_response_client_in_requested_transaction_with_none_header_token()
    {
        // get the response with transactionID and header Auth token of first user
        $response = $this->post('api/v3/client', ['transactionId' => '1-1']);
        $this->assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->status());
    }
    public function test_response_client_in_requested_transaction_with_none_user_header_token()
    {
        // get the response with transactionID and header Auth token of first user
        $response = $this->post('api/v3/client', ['transactionId' => '1-1'], $this->headers(User::find(100)));
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
