<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Http\Response as HttpResponse;
use JWTAuth;

class AuthTest extends TestCase
{
    /**
     * User may want to access the main pages without authenticating.
     * User should get access denied
     */
    public function test_get_main_unauthenticated()
    {
        // as a user, try to access the transaction list information page without a JWT token
        $response = $this->call('GET', 'api/v3/transactions/report');
        // I should be blocked
        $this->assertEquals(HttpResponse::HTTP_METHOD_NOT_ALLOWED, $response->status());
        // as a user, try to access the transaction list information page without a JWT token
        $response = $this->call('GET', 'api/v3/transaction/list');
        // I should be blocked
        $this->assertEquals(HttpResponse::HTTP_METHOD_NOT_ALLOWED, $response->status());
        // as a user, try to access the transaction list information page without a JWT token
        $response = $this->call('GET', 'api/v3/transaction');
        // I should be blocked
        $this->assertEquals(HttpResponse::HTTP_METHOD_NOT_ALLOWED, $response->status());
        // as a user, try to access the transaction list information page without a JWT token
        $response = $this->call('GET', 'api/v3/client');
        // I should be blocked
        $this->assertEquals(HttpResponse::HTTP_METHOD_NOT_ALLOWED, $response->status());
    }
}
