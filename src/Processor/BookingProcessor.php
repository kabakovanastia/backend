<?php

namespace App\Processor;

use App\Dto\BookingInput;
use App\Dto\BookingOutput;
use App\Service\CsvBookingService;
use App\Service\UserService;

class BookingProcessor
{
    public function __construct(
        private CsvBookingService $bookingService,
        private UserService $userService
    ) {}

    public function process(BookingInput $data): BookingOutput
    {
        $user = $this->userService->findUserByPhone($data->phone);
        if (!$user) {
            $user = $this->userService->createUser($data->phone);
        }

        $booking = $this->bookingService->createBooking(
            $data->house_id,
            $user->id,
            $data->comment
        );

        $output = new BookingOutput();
        $output->id = $booking->id;
        $output->house_id = $booking->houseId;
        $output->user_id = $booking->userId;
        $output->comment = $booking->comment;
        $output->status = 'Booking created';

        return $output;
    }
}