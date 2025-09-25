<?php

namespace ChallengeQA\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ChallengeQA\Controllers\UserController;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

use ChallengeQA\Config\Database;
use Doctrine\DBAL\Connection;

class UserControllerTest extends TestCase
{
    private UserController $controller;
    private $requestFactory;
    private $responseFactory;

    private Connection $connection;

    protected function setUp(): void
    {
        $this->controller = new UserController();
        $this->requestFactory = new ServerRequestFactory();
        $this->responseFactory = new ResponseFactory();

        $this->connection = Database::getConnection();
        $this->connection->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->connection->isTransactionActive()) {
            $this->connection->rollBack();
        }
    }

    // CT-API-020
    public function testRegisterValidUserAndStrongPassword(): void
    {
        [$request, $response] = $this->makeRequest(
            [
                'email' => 'email@test.com',
                'password' => 'Senh@Sup0st@men7eF0rt*'
            ],
            '/api/user/register'
        );

        $result = $this->controller->register($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertEquals(201, $result->getStatusCode());
        $this->assertTrue($responseBody['success']);
        $this->assertEquals('User registered successfully', $responseBody['message']);
        $this->assertArrayNotHasKey('warning', $responseBody);
    }

    // CT-API-021
    public function testRegisterValidEmailAndWeakPassword(): void
    {
        [$request, $response] = $this->makeRequest(
            [
                'email' => 'email123@test.com',
                'password' => '123'
            ],
            '/api/user/register'
        );

        $result = $this->controller->register($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);

        $this->assertEquals(201, $result->getStatusCode());
        $this->assertTrue($responseBody['success']);
        $this->assertArrayHasKey('warning', $responseBody);
        $this->assertEquals('Password is weak but accepted', $responseBody['warning']);
    }

    // CT-API-022
    public function testRegisterWithInvalidEmailAndWeakPassword(): void
    {
        [$request, $response] = $this->makeRequest(
            [
                'email' => 'testesemarroba',
                'password' => '123'
            ],
            '/api/user/register'
        );

        $result = $this->controller->register($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);
        
        $this->assertEquals(400, $result->getStatusCode());
        $this->assertFalse($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
    }

    // CT-API-023
    public function testRegisterUserWithEmailAlreadyRegistered(): void
    {

        $this->createUser('duplicatedemail@test.com', 'normalpassword');

        [$request, $response] = $this->makeRequest(
            [
                'email' => 'duplicatedemail@test.com',
                'password' => 'differentpassword'
            ],
            '/api/user/register'
        );
        
        $result = $this->controller->register($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);
        
        $this->assertEquals(409, $result->getStatusCode());
        $this->assertFalse($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
    }

    // CT-API-024
    public function testRegisterUserWithSameEmailAndPasswordAlreadyRegistered(): void
    {

        $this->createUser('samecredentials@test.com', 'anypassword');

        [$request, $response] = $this->makeRequest(
            [
                'email' => 'samecredentials@test.com',
                'password' => 'anypassword'
            ],
            '/api/user/register'
        );
        
        $result = $this->controller->register($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);
        
        $this->assertEquals(409, $result->getStatusCode());
        $this->assertFalse($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
    }

    // CT-API-025
    public function testRegisterUserWithBlankPassword(): void
    {

        [$request, $response] = $this->makeRequest(
            [
                'email' => 'differentemail@test.com',
                'password' => ''
            ],
            '/api/user/register'
        );
        
        $result = $this->controller->register($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);
        
        $this->assertEquals(400, $result->getStatusCode());
        $this->assertFalse($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
    }

    // CT-API-026
    public function testRegisterUserWithoutPassword(): void
    {

        [$request, $response] = $this->makeRequest(
            [
                'email' => 'differentemail1@test.com',
            ],
            '/api/user/register'
        );
        
        $result = $this->controller->register($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);
        
        $this->assertEquals(400, $result->getStatusCode());
        $this->assertFalse($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
    }

    // CT-API-027
    public function testRegisterUserWithoutEmail(): void
    {

        [$request, $response] = $this->makeRequest(
            [
                'password' => 'anypassword',
            ],
            '/api/user/register'
        );
        
        $result = $this->controller->register($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);
        
        $this->assertEquals(400, $result->getStatusCode());
        $this->assertFalse($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
    }

    // CT-API-028
    public function testRegisterUserWithBlankEmail(): void
    {

        [$request, $response] = $this->makeRequest(
            [
                'email' => '',
                'password' => 'anypassword',
            ],
            '/api/user/register'
        );
        
        $result = $this->controller->register($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);
        
        $this->assertEquals(400, $result->getStatusCode());
        $this->assertFalse($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
    }

    // CT-API-029
    public function testLoginWithValidCredentials(): void
    {
        $this->createUser('validemail@test.com', 'mbqu2Q39NX1UB#');

        [$request, $response] = $this->makeRequest(
            [
                'email' => 'validemail@test.com',
                'password' => 'mbqu2Q39NX1UB#'
            ],
            '/api/user/login'
        );

        $result = $this->controller->login($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);
        
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertTrue($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
        
    }

    // CT-API-030
    public function testLoginWithNonExistentUser(): void
    {
        [$request, $response] = $this->makeRequest(
            [
                'email' => 'nonexistent@example.com',
                'password' => 'anypassword'
            ],
            '/api/user/login'
        );

        $result = $this->controller->login($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);
        
        $this->assertEquals(401, $result->getStatusCode());
        $this->assertFalse($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
        
    }

    // CT-API-031
    public function testLoginWithInvalidCredentials(): void
    {

        $this->createUser('invalidpasswordvalidemail@test.com', 'invalidpassword');
        
        [$request, $response] = $this->makeRequest(
            [
                'email' => 'invalidpasswordvalidemail@test.com',
                'password' => 'mbqu232Q39NX1UB#'
            ],
            '/api/user/login'
        );

        $result = $this->controller->login($request, $response);
        $responseBody = json_decode((string) $result->getBody(), true);
        
        $this->assertEquals(401, $result->getStatusCode());
        $this->assertFalse($responseBody['success']);
        $this->assertNotEquals('Password is incorrect', $responseBody['message']);
    }

    protected function makeRequest(array $requestData, string $endpoint): array
    {
        $request = $this->requestFactory
            ->createServerRequest('POST', $endpoint)
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(json_encode($requestData));
        $request->getBody()->rewind();
        $response = $this->responseFactory->createResponse();

        return [$request, $response];
    }

    protected function createUser(string $email, string $password): void
    {
        $this->connection->insert('users', [
            'email' => $email,
            'password' => $password,
        ]);
    }
}