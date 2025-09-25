<?php

namespace ChallengeQA\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ChallengeQA\Controllers\CalculatorController;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

class CalculatorControllerTest extends TestCase
{
    private CalculatorController $controller;
    private $requestFactory;
    private $responseFactory;

    protected function setUp(): void
    {
        $this->controller = new CalculatorController();
        $this->requestFactory = new ServerRequestFactory();
        $this->responseFactory = new ResponseFactory();
    }

    // CT-API-003
    public function testSimpleInterestCalculation(): void
    {

        [$request, $response] = $this->makeRequest(
            [
                'principal' => 1000,
                'rate' => 5,
                'time' => 2
            ],
            '/api/calculator/simple-interest'
        );

        $result = $this->controller->simpleInterest($request, $response);
        
        $this->assertEquals(200, $result->getStatusCode());
        
        $responseBody = json_decode((string) $result->getBody(), true);
        $this->assertTrue($responseBody['success']);
        $this->assertEquals('simple_interest', $responseBody['calculation_type']);
        $this->assertEquals(1100, $responseBody['results']['total_amount']);
    }

    // CT-API-004
    public function testSimpleInterestInvalidCalculation(): void
    {

        [$request, $response] = $this->makeRequest(
            [
                'principal' => -1000,
                'rate' => 5,
                'time' => 2
            ],
            '/api/calculator/simple-interest'
        );

        $result = $this->controller->simpleInterest($request, $response);

        $this->assertEquals(400, $result->getStatusCode());

        $responseBody = json_decode((string) $result->getBody(), true);
        $this->assertFalse($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
    }

    // CT-API-005
    public function testSimpleInterestCalculationWithMissingParameters(): void
    {

        [$request, $response] = $this->makeRequest(
            [
                'rate' => 5,
                'time' => 2
            ],
            '/api/calculator/simple-interest'
        );

        $result = $this->controller->simpleInterest($request, $response);

        $this->assertEquals(400, $result->getStatusCode());

        $responseBody = json_decode((string) $result->getBody(), true);
        $this->assertFalse($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
    }

    // CT-API-008
    public function testCompoundInterestCalculation(): void
    {

        [$request, $response] = $this->makeRequest(
            [
                'principal' => 1000,
                'rate' => 5,
                'time' => 2,
                'compounding_frequency' => 12
            ],
            '/api/calculator/compound-interest'
        );

        $result = $this->controller->compoundInterest($request, $response);
        
        $this->assertEquals(200, $result->getStatusCode());
        
        $responseBody = json_decode((string) $result->getBody(), true);
        $this->assertTrue($responseBody['success']);
        $this->assertEquals('compound_interest', $responseBody['calculation_type']);
        $this->assertEquals(1104.9, $responseBody['results']['total_amount']);
    }

    // CT-API-009
    public function testCompoundInterestCalculationWithParameterCompoundingFrequencyEqualsToZero(): void
    {

        [$request, $response] = $this->makeRequest(
            [
                'principal' => 1000,
                'rate' => 5,
                'time' => 2,
                'compounding_frequency' => 0
            ],
            '/api/calculator/compound-interest'
        );

        $result = $this->controller->compoundInterest($request, $response);
        
        $this->assertEquals(400, $result->getStatusCode());
        
        $responseBody = json_decode((string) $result->getBody(), true);
        $this->assertFalse($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
    }

    // CT-API-010
    public function testCompoundInterestCalculationWithParameterCompoundingFreequencyLessThan0(): void
    {

        [$request, $response] = $this->makeRequest(
            [
                'principal' => 1000,
                'rate' => 5,
                'time' => 2,
                'compounding_frequency' => -1
            ],
            '/api/calculator/compound-interest'
        );

        $result = $this->controller->compoundInterest($request, $response);

        $this->assertEquals(400, $result->getStatusCode());

        $responseBody = json_decode((string) $result->getBody(), true);
        $this->assertFalse($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
    }

    // CT-API-011
    public function testCompoundInterestCalculationWithNegativeNumbers(): void
    {

        [$request, $response] = $this->makeRequest(
            [
                'principal' => -1000,
                'rate' => -5,
                'time' => -2,
                'compounding_frequency' => -12
            ],
            '/api/calculator/compound-interest'
        );

        $result = $this->controller->compoundInterest($request, $response);

        $this->assertEquals(400, $result->getStatusCode());

        $responseBody = json_decode((string) $result->getBody(), true);
        $this->assertFalse($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
    }

    // CT-API-012
    public function testCompoundInterestWithoutCompoundingFrequency(): void
    {

        [$request, $response] = $this->makeRequest(
            [
                'principal' => 1000,
                'rate' => 5,
                'time' => 2,
            ],
            '/api/calculator/compound-interest'
        );

        $result = $this->controller->compoundInterest($request, $response);

        $this->assertEquals(200, $result->getStatusCode());

        $responseBody = json_decode((string) $result->getBody(), true);
        $this->assertTrue($responseBody['success']);
        $this->assertEquals(12, $responseBody['inputs']['compounding_frequency']);
    }

    // CT-API-013
    public function testCompoundInterestWithoutRequiredParameters(): void
    {

        [$request, $response] = $this->makeRequest(
            [
                'principal' => 1000,
                'rate' => 5,
            ],
            '/api/calculator/compound-interest'
        );

        $result = $this->controller->compoundInterest($request, $response);

        $this->assertEquals(400, $result->getStatusCode());

        $responseBody = json_decode((string) $result->getBody(), true);
        $this->assertFalse($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
    }

    // CT-API-014
    public function testInstallmentCalculation(): void
    {

        [$request, $response] = $this->makeRequest(
            [
                'principal' => 1000,
                'rate' => 5,
                'installments' => 10
            ],
            '/api/calculator/installment'
        );

        $result = $this->controller->installmentSimulation($request, $response);

        $this->assertEquals(200, $result->getStatusCode());

        $responseBody = json_decode((string) $result->getBody(), true);
        $this->assertTrue($responseBody['success']);
        $this->assertEquals(102.3, $responseBody['results']['installment_amount']);
        $this->assertEquals(1023.06, $responseBody['results']['total_amount']);
        $this->assertEquals(23.06, $responseBody['results']['total_interest']);
    }

    // CT-API-015
    public function testInvalidInstallmentCalculation(): void
    {

        [$request, $response] = $this->makeRequest(
            [
                'principal' => -1000,
                'rate' => 5,
                'installments' => 10
            ],
            '/api/calculator/installment'
        );

        $result = $this->controller->installmentSimulation($request, $response);

        $this->assertEquals(400, $result->getStatusCode());

        $responseBody = json_decode((string) $result->getBody(), true);
        $this->assertFalse($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
    }

    // CT-API-016
    public function testInstallmentCalculationParametersWithText(): void
    {

        [$request, $response] = $this->makeRequest(
            [
                'principal' => "A",
                'rate' => "A",
                'installments' => "A"
            ],
            '/api/calculator/installment'
        );

        $result = $this->controller->installmentSimulation($request, $response);

        $this->assertEquals(400, $result->getStatusCode());

        $responseBody = json_decode((string) $result->getBody(), true);
        $this->assertFalse($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
    }

    // CT-API-017
    public function testInstallmentCalculationWithoutRequiredParameters(): void
    {

        [$request, $response] = $this->makeRequest(
            [
                'principal' => 1000,
                'installments' => 10
            ],
            '/api/calculator/installment'
        );

        $result = $this->controller->installmentSimulation($request, $response);

        $this->assertEquals(400, $result->getStatusCode());

        $responseBody = json_decode((string) $result->getBody(), true);
        $this->assertFalse($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
    }

    // CT-API-018
    public function testInstallmentCalculationWithInstallmentsNumberNegative(): void
    {

        [$request, $response] = $this->makeRequest(
            [
                'principal' => 1000,
                'rate' => 5,
                'installments' => -10
            ],
            '/api/calculator/installment'
        );

        $result = $this->controller->installmentSimulation($request, $response);

        $this->assertEquals(400, $result->getStatusCode());

        $responseBody = json_decode((string) $result->getBody(), true);
        $this->assertFalse($responseBody['success']);
        $this->assertArrayHasKey('message', $responseBody);
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
}