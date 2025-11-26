<?php

namespace App\Tests\Integration\Api;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserService $userService;
    private UserPasswordHasherInterface $passwordHasher;
    private string $testUserCsvPath;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        $this->userService = $container->get(UserService::class);
        $this->passwordHasher = $container->get(UserPasswordHasherInterface::class);
        $reflection = new \ReflectionClass($this->userService);
        $property = $reflection->getProperty('filePath');
        $property->setAccessible(true);
        $this->testUserCsvPath = $property->getValue($this->userService);
        file_put_contents($this->testUserCsvPath, "id,phone,name,password\n");
    }

    protected function tearDown(): void
    {
        file_put_contents($this->testUserCsvPath, "id,phone,name,password\n");
    }

    public function testLoginSuccess(): void
    {

        $phone = '+79991234567';
        $plainPassword = 'testpassword123';
        $hashedPassword = $this->passwordHasher->hashPassword(new User(0, $phone), $plainPassword);
        $user = $this->userService->createUser($phone, 'Test User', $hashedPassword);


        $this->client->request(Request::METHOD_POST, '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'username' => $user->phone,
            'password' => $plainPassword,
        ]));


        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('user', $responseData);
        $this->assertEquals($user->phone, $responseData['user']);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Login successful', $responseData['message']);
    }

    public function testLoginFailure(): void
    {
        $phone = '+79991234568';
        $plainPassword = 'correctpassword';
        $hashedPassword = $this->passwordHasher->hashPassword(new User(0, $phone), $plainPassword);
        $user = $this->userService->createUser($phone, 'Test User 2', $hashedPassword);


        $this->client->request(Request::METHOD_POST, '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'username' => $user->phone,
            'password' => 'wrongpassword',
        ]));

        // Проверки
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testLogout(): void
    {
        $phone = '+79991234569';
        $plainPassword = 'logoutpassword';
        $hashedPassword = $this->passwordHasher->hashPassword(new User(0, $phone), $plainPassword);
        $user = $this->userService->createUser($phone, 'Test User 3', $hashedPassword);

        $this->client->request(Request::METHOD_POST, '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'username' => $user->phone,
            'password' => $plainPassword,
        ]));

        $this->assertResponseIsSuccessful();
        $this->client->request(Request::METHOD_POST, '/api/logout');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Logged out successfully', $responseData['message']);
        $this->client->request(Request::METHOD_GET, '/api/houses/available');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testAccessToProtectedApiRequiresAuth(): void
    {
        $this->client->request(Request::METHOD_GET, '/api/houses/available');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}