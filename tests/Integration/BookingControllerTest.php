<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingControllerTest extends WebTestCase
{
    public function testCreateBooking(): void
    {
        $client = static::createClient();

        // Ensure house exists (e.g., create dummy house file beforehand in test env)
        $client->request('POST', '/api/bookings', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'house_id' => 1,
            'phone' => '+79991234567',
            'comment' => 'Test booking'
        ]));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals(1, $data['house_id']);
        $this->assertEquals('Test booking', $data['comment']);
    }
}