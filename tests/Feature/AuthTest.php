<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Response as HttpResponse;

class AuthTest extends TestCase
{
    /**
     * User may want to access the main pages without authenticating.
     * User should get access denied
     */
    public function testGetMainUnauthenticated()
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

    /**
     * User may want to login, but using wrong credentials.
     * This route should be free for all unauthenticated users.
     * Users should be warned when login fails
     */
    public function testLoginWithWrongData()
    {
        // as a user, I wrongly type my email and password
        $data = ['email' => 'email', 'password' => 'password'];
        // and I submit it to the login api
        $response = $this->call('POST', 'api/v3/merchant/user/login', $data);
        // I shouldnt be able to login with wrong data
        $this->assertEquals(HttpResponse::HTTP_UNAUTHORIZED, $response->status());
    }

    /**
     * User may want to login.
     * This route should be free for all unauthenticated users.
     * User should receive an JWT token
     */
    public function testLoginSuccesfully()
    {
        // as a user, I type my email and password
        $data = ['email' => 'merchant@test.com', 'password' => '123*-+'];
        // and I submit it to the login api
        $response = $this->call('POST', 'api/v3/merchant/user/login', $data);
        // I should be able to login
        $this->assertEquals(HttpResponse::HTTP_OK, $response->status());
        // assert there is a TOKEN on the response
        $content = json_decode($response->getContent());
        $this->assertObjectHasAttribute('token', $content);
        $this->assertNotEmpty($content->token);
    }
}
