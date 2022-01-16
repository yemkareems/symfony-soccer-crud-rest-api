<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class APIControllerTest extends WebTestCase
{
	private $username = 'kareem@gmail.com';
	private $password = 'kareem';

    
	public function testLoginWithValidCredentials()
	{
		$client = static::createClient();

		$client->request('POST', '/api/login_check', [
			'username' => $this->username,
			'password' => $this->password
		]);
		$response = json_decode($client->getResponse()->getContent(), true);
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertArrayHasKey('token', $response);
		return $response['token'];


	}

	public function testCreateTeamTest() {

		$token = $this->testLoginWithValidCredentials();
		self::ensureKernelShutdown();
		$client = static::createClient();
        	$client->request('POST', '/api/team/create', ['teamName' => 'unit Test Team', 'teamLogo' => 'https://thumbs.dreamstime.com/b/arsenal-logo-english-football-club-fc-emblem-football-shirt-98653549.jpg'], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . $token]);

		$this->assertEquals(200, $client->getResponse()->getStatusCode());

	}

	public function testCreateTeamValidationTest() {

		$token = $this->testLoginWithValidCredentials();
		self::ensureKernelShutdown();
		$client = static::createClient();
        	$client->request('POST', '/api/team/create', ['teamName' => '', 'teamLogo' => 'https://thumbs.dreamstime.com/b/arsenal-logo-english-football-club-fc-emblem-football-shirt-98653549.jpg'], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . $token]);

		$this->assertEquals(500, $client->getResponse()->getStatusCode());

	}

	public function testAnonymousAccessToTeamListTest() {

		$client = static::createClient();
        	$client->request('GET', '/list/teams', [], [], []);

		$this->assertEquals(200, $client->getResponse()->getStatusCode());

	}

	public function testAnonymousAccessToProtectedRouteTest() {

		$client = static::createClient();
        	$client->request('POST', '/api/team/create', [], [], []);

		$this->assertEquals(401, $client->getResponse()->getStatusCode());

	}
}
