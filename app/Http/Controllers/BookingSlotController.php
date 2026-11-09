<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Services\SlotCalculationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingSlotController extends Controller
{
    public function __construct(
        protected SlotCalculationService $slotCalculationService
    ) {}

    public function index(Request $request, Room $room): JsonResponse
    {
        $date = $request->query('date', Carbon::today()->toDateString());
        $excludeBookingId = $request->query('exclude_booking_id') ? (int) $request->query('exclude_booking_id') : null;

        $slotData = $this->slotCalculationService->getAvailableSlots($room, $date, $excludeBookingId);

        return response()->json([
            'room' => [
                'id' => $room->id,
                'slug' => $room->slug,
                'name' => $room->localized_name,
                'capacity' => $room->capacity,
            ],
            'date' => $date,
            'is_holiday' => $slotData['is_holiday'],
            'is_closed' => $slotData['is_closed'],
            'open_time' => $slotData['open_time'] ?? null,
            'close_time' => $slotData['close_time'] ?? null,
            'slots' => $slotData['slots'],
        ]);
    }
}
