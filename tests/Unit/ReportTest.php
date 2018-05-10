<?php

namespace Tests\Unit;

use App\Http\Resources\Client;
use App\Transaction;
use App\User;
use Tests\TestCase;
use JWTAuth;
use Illuminate\Http\Response as HttpResponse;

class ReportTest extends TestCase
{
    /**
     * A test in transaction report with requested transaction.
     *
     * @return void
     */
    public function test_response_report_in_requested_parameters()
    {
        // get the response with request and header Auth token of first user
        $response = $this->post(
            'api/v3/transactions/report',
            [
                'fromDate'  => '2015-07-01',
                'toDate'    => '2015-10-01',
                'merchant'  => 1,
                'acquirer'  => 1
            ],
            $this->headers(User::first())
        );
        $this->assertEquals(HttpResponse::HTTP_OK, $response->status());
        // get client of transaction from DB
        $transaction = Transaction::where(
            [
                'fromDate'      => '2015-07-01',
                'toDate'        => '2015-10-01',
                'merchant_id'   => 1,
                'acquirer_id'   => 1
            ]
        )->get();
        $this->assertNotNull($transaction);
        $transactionResource = new Client($transaction);
        // Check response data and collection of data from DB
        for($i=0; $i < count($response->getData()->response); $i++){
            $temp = [
                'count'     => $transactionResource[$i]->count,
                'total'     => $transactionResource[$i]->total,
                'currency'  => $transactionResource[$i]->currency
            ];
            $this->assertEquals((object)$temp, $response->getData()->response[$i]);
        }
    }
    public function test_response_report_in_invalid_requested_parameters()
    {
        $response = $this->post(
            'api/v3/transactions/report',
            [
                'fromDate'  => '2018-07-01',
                'toDate'    => '2018-10-01',
                'merchant'  => 3,
                'acquirer'  => 2
            ],
            $this->headers(User::first())
        );
        $this->assertEquals(HttpResponse::HTTP_OK, $response->status());
        // get client of transaction from DB
        $transaction = Transaction::where(
            [
                'fromDate'      => '2018-07-01',
                'toDate'        => '2018-10-01',
                'merchant_id'   => 3,
                'acquirer_id'   => 2
            ]
        )->get();
        //dd($transaction);
        $this->assertEmpty($transaction);
    }
    public function test_response_report_in_none_requested_parameters()
    {
        $response = $this->post('api/v3/transactions/report');
        $this->assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->status());
    }
    public function test_response_report_in_none_requested_parameters_with_header_token()
    {
        $response = $this->post('api/v3/transactions/report', $this->headers(User::first()));
        $this->assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->status());
    }
    public function test_response_report_in_requested_parameters_with_none_header_token()
    {
        $response = $this->post(
            'api/v3/transactions/report',
            [
                'fromDate'      => '2018-07-01',
                'toDate'        => '2018-10-01',
                'merchant_id'   => 3,
                'acquirer_id'   => 2
            ]
        );
        $this->assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->status());
    }
    public function test_response_report_in_requested_parameters_with_none_user_header_token()
    {
        $response = $this->post(
            'api/v3/transactions/report',
            [
                'fromDate'      => '2018-07-01',
                'toDate'        => '2018-10-01',
                'merchant_id'   => 3,
                'acquirer_id'   => 2
            ],
            $this->headers(User::find(100)));
        $this->assertEquals(HttpResponse::HTTP_BAD_REQUEST, $response->status());
    }
    public function test_response_report_in_missing_requested_parameters()
    {
        $response = $this->post(
            'api/v3/transactions/report',
            [
                'fromDate'      => '2018-07-01',
                'toDate'        => '2018-10-01',
                'merchant_id'   => 3,
            ],
            $this->headers(User::first())
        );
        $this->assertEquals('DECLINED', $response->getData()->status);
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
