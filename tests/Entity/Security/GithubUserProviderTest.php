<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use App\Security\GithubUserProvider;
use GuzzleHttp\Client;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class GithubUserProviderTest extends TestCase
{
    private MockObject|Client|null $client;
    private MockObject|SerializerInterface|null $serializer;
    private MockObject|StreamInterface|null $streamedResponse;
    private MockObject|ResponseInterface|null $response;
    protected function setUp(): void
    {
        $this->client = $this->getMockBuilder('GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $this->serializer = $this->getMockBuilder('JMS\\Serializer\\SerializerInterface')
            ->getMock();

        $this->streamedResponse = $this->getMockBuilder('Psr\Http\Message\StreamInterface')
            ->getMock();

        $this->response = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')
            ->getMock();
    }


    public function testloadUserByUsernameReturningAUser()
    {
        // Mock du client HTTP
        $this->client
            ->expects($this->once())
            ->method('get')
            ->willReturn($this->response);


        // Mock de la réponse HTTP
        $this->response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->streamedResponse);

        $this->streamedResponse
            ->expects($this->once())
            ->method('getContents')
            ->willReturn('foo');

        $userData = [
            'login' => 'a login',
            'name' => 'user name',
            'email' => 'adress@mail.com',
            'avatar_url' => 'url to the avatar',
            'html_url' => 'url to profile'
        ];

        // Mock du serializer
        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn($userData);

        // Test
        $githubUserProvider = new GithubUserProvider($this->client, $this->serializer);
        $user = $githubUserProvider->loadUserByUsername('an-access-token');

        // Assertions

        $expectedUser = new User(
            $userData['login'],
            $userData['name'],
            $userData['email'],
            $userData['avatar_url'],
            $userData['html_url']
        );
        $this->assertEquals($expectedUser, $user);
        $this->assertEquals('App\Entity\User', get_class($user));

        // $this->assertInstanceOf(User::class, $user);
        // $this->assertEquals('testuser', $user->getUsername());
        // $this->assertEquals('Test User', $user->getFullname());
        // $this->assertEquals('test@example.com', $user->getEmail());

    }

    public function testLoadUserByUsernameThrowingException()
    {
        $this->client
            ->expects($this->once())
            ->method('get')
            ->willReturn($this->response);

        $this->response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->streamedResponse);

        $this->streamedResponse
            ->expects($this->once())
            ->method('getContents')
            ->willReturn('foo');

        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn([]);

        $this->expectException('LogicException');

        $githubUserProvider = new GithubUserProvider($this->client, $this->serializer);

        $githubUserProvider->loadUserByUsername('an-access-token');
    }
}
