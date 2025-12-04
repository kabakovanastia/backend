<?php

namespace App\Processor;

use App\ApiResource\BookingUpdateOutput;
use App\ApiResource\BookingUpdateInput;
use App\Service\CsvBookingService;

class BookingUpdateProcessor
{
    public function __construct(private CsvBookingService $bookingService) {}

    public function process(BookingUpdateInput $data, array $uriVariables): BookingUpdateOutput
    {
        $id = (int) $uriVariables['id'];
        $updated = $this->bookingService->updateBooking($id, $data->comment);

        if (!$updated) {
            throw new \Exception('Booking not found', 404);
        }

        $output = new BookingUpdateOutput();
        $output->status = 'Comment updated';
        return $output;
    }
}