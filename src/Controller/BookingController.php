<?php

namespace App\Controller;

use App\Service\CsvHouseService;
use App\Service\CsvBookingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    public function __construct(
        private CsvHouseService $houseService,
        private CsvBookingService $bookingService
    ) {}

    #[Route('/api/houses/available', name: 'houses_available', methods: ['GET'])]
    public function getAvailableHouses(): JsonResponse
    {
        return $this->json($this->houseService->getAllHouses());
    }

    #[Route('/api/bookings', name: 'create_booking', methods: ['POST'])]
    public function createBooking(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $houseId = $data['house_id'] ?? null;
        $phone = $data['phone'] ?? null;
        $comment = $data['comment'] ?? '';

        if (!$houseId || !$phone) {
            return $this->json(['error' => 'Missing house_id or phone'], 400);
        }

        if (!$this->houseService->getHouseById($houseId)) {
            return $this->json(['error' => 'House not found'], 404);
        }

        $this->bookingService->createBooking($houseId, $phone, $comment);

        return $this->json(['status' => 'Booking created'], 201);
    }

    #[Route('/api/bookings/{id}', name: 'update_booking', methods: ['PUT'])]
    public function updateBooking(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $comment = $data['comment'] ?? '';

        $updated = $this->bookingService->updateBooking($id, $comment);
        if (!$updated) {
            return $this->json(['error' => 'Booking not found'], 404);
        }

        return $this->json(['status' => 'Comment updated']);
    }
}