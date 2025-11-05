<?php

namespace App\Controller;

use App\Service\CsvHouseService;
use App\Service\CsvBookingService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    public function __construct(
        private CsvHouseService $houseService,
        private CsvBookingService $bookingService,
        private UserService $userService
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
            throw new HttpException(400, 'Missing house_id or phone');
        }

        if (!$this->houseService->getHouseById($houseId)) {
            throw new HttpException(404, 'House not found');
        }

        $user = $this->userService->findUserByPhone($phone);
        if (!$user) {
            $user = $this->userService->createUser($phone);
        }

        $booking = $this->bookingService->createBooking($houseId, $user->id, $comment);

        return $this->json([
            'id' => $booking->id,
            'house_id' => $booking->houseId,
            'user_id' => $booking->userId,
            'comment' => $booking->comment,
            'status' => 'Booking created'
        ], 201);
    }

    #[Route('/api/bookings/{id}', name: 'update_booking', methods: ['PUT'])]
    public function updateBooking(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $comment = $data['comment'] ?? '';

        $updated = $this->bookingService->updateBooking($id, $comment);
        if (!$updated) {
            throw new HttpException(404, 'Booking not found');
        }

        return $this->json(['status' => 'Comment updated']);
    }
}