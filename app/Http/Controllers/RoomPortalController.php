<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomEquipment;
use App\Services\RoomStatusService;
use App\Services\SettingService;
use App\Services\SlotCalculationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomPortalController extends Controller
{
    public function __construct(
        protected RoomStatusService $roomStatusService,
        protected SlotCalculationService $slotCalculationService,
        protected SettingService $settingService
    ) {}

    public function index(Request $request): View
    {
        $tz = $this->settingService->officeTimezone();
        $today = Carbon::today($tz);
        $maxDays = $this->settingService->maxDaysAhead();

        $selectedDate = $request->query('date', $today->toDateString());
        try {
            $parsedDate = Carbon::parse($selectedDate, $tz);
            if ($parsedDate->lessThan($today) || $parsedDate->greaterThan($today->copy()->addDays($maxDays))) {
                $selectedDate = $today->toDateString();
            }
        } catch (\Exception) {
            $selectedDate = $today->toDateString();
        }

        $query = Room::with('equipments')->active()->orderBy('sort_order');

        if ($request->filled('room_id')) {
            $query->where('id', $request->room_id);
        }

        if ($request->filled('capacity')) {
            $query->where('capacity', '>=', (int) $request->capacity);
        }

        if ($request->filled('equipment')) {
            $query->whereHas('equipments', function ($q) use ($request) {
                $q->where('code', $request->equipment);
            });
        }

        $rooms = $query->get();

        $roomsWithStatus = $rooms->map(function ($room) use ($selectedDate) {
            $status = $this->roomStatusService->getRoomStatus($room);
            $slotData = $this->slotCalculationService->getAvailableSlots($room, $selectedDate);
            $freeSlotsCount = count($slotData['slots'] ?? []);

            return [
                'room' => $room,
                'status' => $status,
                'free_slots_count' => $freeSlotsCount,
                'is_holiday' => $slotData['is_holiday'] ?? false,
                'is_closed' => $slotData['is_closed'] ?? false,
            ];
        });

        $allRooms = Room::active()->orderBy('sort_order')->get(['id', 'slug', 'name_en', 'name_sr']);
        $allEquipments = RoomEquipment::select('code', 'label_en', 'label_sr')->distinct()->get();

        return view('pages.rooms.index', [
            'roomsWithStatus' => $roomsWithStatus,
            'allRooms' => $allRooms,
            'allEquipments' => $allEquipments,
            'selectedDate' => $selectedDate,
            'selectedRoomId' => $request->query('room_id'),
            'selectedCapacity' => $request->query('capacity'),
            'selectedEquipment' => $request->query('equipment'),
            'maxDate' => $today->copy()->addDays($maxDays)->toDateString(),
            'todayDate' => $today->toDateString(),
        ]);
    }
}
