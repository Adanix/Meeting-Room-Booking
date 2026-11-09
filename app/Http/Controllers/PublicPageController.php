<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Services\RoomStatusService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function __construct(
        protected RoomStatusService $roomStatusService,
        protected SettingService $settingService
    ) {}

    public function home(Request $request): View
    {
        // 3 active preview rooms for landing page live status preview widget
        $previewRooms = Room::with('equipments')
            ->active()
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        $roomsWithStatus = $previewRooms->map(function ($room) {
            return [
                'room' => $room,
                'status' => $this->roomStatusService->getRoomStatus($room),
            ];
        });

        $totalActiveRooms = Room::active()->count();

        return view('pages.home', [
            'roomsWithStatus' => $roomsWithStatus,
            'totalActiveRooms' => $totalActiveRooms,
            'contactEmail' => config('demo.contact_email', 'nixresponding@gmail.com'),
        ]);
    }

    public function docs(Request $request): View
    {
        return view('pages.docs', [
            'contactEmail' => config('demo.contact_email', 'nixresponding@gmail.com'),
            'settings' => $this->settingService->all(),
        ]);
    }
}
