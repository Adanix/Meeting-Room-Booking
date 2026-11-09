<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomEquipment;
use App\Services\RoomPhotoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoomManagementController extends Controller
{
    public function __construct(
        protected RoomPhotoService $photoService
    ) {}

    public function index(): View
    {
        $rooms = Room::with('equipments')->orderBy('sort_order')->get();

        return view('admin.rooms.index', compact('rooms'));
    }

    public function create(): View
    {
        $standardEquipments = [
            ['code' => 'tv_4k', 'en' => '4K TV Screen', 'sr' => '4K TV Ekran'],
            ['code' => 'projector', 'en' => 'Projector', 'sr' => 'Projektor'],
            ['code' => 'video_conf', 'en' => 'Video Conferencing System', 'sr' => 'Video konferencijski sistem'],
            ['code' => 'whiteboard', 'en' => 'Whiteboard / Glassboard', 'sr' => 'Bela / Staklena tabla'],
            ['code' => 'speakerphone', 'en' => 'Conference Speakerphone', 'sr' => 'Konferencijski mikrofon'],
            ['code' => 'hdmi_usbc', 'en' => 'HDMI & USB-C Adapters', 'sr' => 'HDMI & USB-C adapteri'],
            ['code' => 'catering', 'en' => 'Catering Allowed', 'sr' => 'Ketering dozvoljen'],
            ['code' => 'ac', 'en' => 'Air Conditioning', 'sr' => 'Klima uređaj'],
        ];

        return view('admin.rooms.create', compact('standardEquipments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_en' => ['required', 'string', 'max:100'],
            'name_sr' => ['required', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:200'],
            'description_en' => ['nullable', 'string', 'max:500'],
            'description_sr' => ['nullable', 'string', 'max:500'],
            'color_hex' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'equipments' => ['nullable', 'array'],
            'photo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $slug = Str::slug($validated['name_en']);
        $baseSlug = $slug;
        $counter = 1;
        while (Room::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $this->photoService->uploadPhoto($request->file('photo'));
        }

        $room = Room::create([
            'slug' => $slug,
            'name_en' => $validated['name_en'],
            'name_sr' => $validated['name_sr'],
            'location' => $validated['location'],
            'capacity' => (int) $validated['capacity'],
            'description_en' => $validated['description_en'] ?? null,
            'description_sr' => $validated['description_sr'] ?? null,
            'color_hex' => $validated['color_hex'] ?? '#4F46E5',
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'photo_path' => $photoPath,
        ]);

        if (! empty($validated['equipments'])) {
            foreach ($validated['equipments'] as $eq) {
                if (isset($eq['code'], $eq['label_en'], $eq['label_sr'])) {
                    RoomEquipment::create([
                        'room_id' => $room->id,
                        'code' => $eq['code'],
                        'label_en' => $eq['label_en'],
                        'label_sr' => $eq['label_sr'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.rooms.index')
            ->with('status', "Room '{$room->name_en}' created successfully.");
    }

    public function edit(Room $room): View
    {
        $room->load('equipments');
        $standardEquipments = [
            ['code' => 'tv_4k', 'en' => '4K TV Screen', 'sr' => '4K TV Ekran'],
            ['code' => 'projector', 'en' => 'Projector', 'sr' => 'Projektor'],
            ['code' => 'video_conf', 'en' => 'Video Conferencing System', 'sr' => 'Video konferencijski sistem'],
            ['code' => 'whiteboard', 'en' => 'Whiteboard / Glassboard', 'sr' => 'Bela / Staklena tabla'],
            ['code' => 'speakerphone', 'en' => 'Conference Speakerphone', 'sr' => 'Konferencijski mikrofon'],
            ['code' => 'hdmi_usbc', 'en' => 'HDMI & USB-C Adapters', 'sr' => 'HDMI & USB-C adapteri'],
            ['code' => 'catering', 'en' => 'Catering Allowed', 'sr' => 'Ketering dozvoljen'],
            ['code' => 'ac', 'en' => 'Air Conditioning', 'sr' => 'Klima uređaj'],
        ];

        return view('admin.rooms.edit', [
            'room' => $room,
            'standardEquipments' => $standardEquipments,
            'existingEquipmentCodes' => $room->equipments->pluck('code')->toArray(),
        ]);
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'name_en' => ['required', 'string', 'max:100'],
            'name_sr' => ['required', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:200'],
            'description_en' => ['nullable', 'string', 'max:500'],
            'description_sr' => ['nullable', 'string', 'max:500'],
            'color_hex' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'equipments' => ['nullable', 'array'],
            'photo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            $this->photoService->deletePhoto($room->photo_path);
            $room->photo_path = $this->photoService->uploadPhoto($request->file('photo'));
        }

        $room->update([
            'name_en' => $validated['name_en'],
            'name_sr' => $validated['name_sr'],
            'location' => $validated['location'],
            'capacity' => (int) $validated['capacity'],
            'description_en' => $validated['description_en'] ?? null,
            'description_sr' => $validated['description_sr'] ?? null,
            'color_hex' => $validated['color_hex'] ?? $room->color_hex,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'photo_path' => $room->photo_path,
        ]);

        // Update equipment
        RoomEquipment::where('room_id', $room->id)->delete();
        if (! empty($validated['equipments'])) {
            foreach ($validated['equipments'] as $eq) {
                if (isset($eq['code'], $eq['label_en'], $eq['label_sr'])) {
                    RoomEquipment::create([
                        'room_id' => $room->id,
                        'code' => $eq['code'],
                        'label_en' => $eq['label_en'],
                        'label_sr' => $eq['label_sr'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.rooms.index')
            ->with('status', "Room '{$room->name_en}' updated successfully.");
    }

    public function toggleActive(Room $room): RedirectResponse
    {
        $room->update(['is_active' => ! $room->is_active]);
        $state = $room->is_active ? 'visible on public portal' : 'hidden from public portal';

        return back()->with('status', "Room '{$room->name_en}' is now {$state}.");
    }
}
