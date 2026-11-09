<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\CompanyEvent;
use App\Models\Holiday;
use App\Models\Room;
use App\Models\RoomEquipment;
use App\Models\Setting;
use App\Models\SimulatedEmail;
use App\Models\User;
use App\Models\WorkingHour;
use App\Services\CalendarLinkService;
use App\Services\IcsGeneratorService;
use App\Services\SimulatedEmailDeliveryService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $tz = 'Europe/Belgrade';
        $today = Carbon::today($tz);

        // Ensure room demo photos exist in storage/app/public/rooms
        $demoRoomsSource = public_path('images/rooms');
        $storageRoomsDest = storage_path('app/public/rooms');
        if (File::isDirectory($demoRoomsSource)) {
            if (!File::isDirectory($storageRoomsDest)) {
                File::makeDirectory($storageRoomsDest, 0755, true);
            }
            foreach (File::files($demoRoomsSource) as $file) {
                $destFile = $storageRoomsDest . '/' . $file->getFilename();
                if (!File::exists($destFile)) {
                    File::copy($file->getPathname(), $destFile);
                }
            }
        }

        // 1. Seed Users
        $admin = User::updateOrCreate(
            ['email' => 'admin@rooms-demo.local'],
            [
                'name' => 'Nikola Jovanović',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'preferred_locale' => 'en',
            ]
        );

        $coordinator = User::updateOrCreate(
            ['email' => 'coordinator@rooms-demo.local'],
            [
                'name' => 'Ana Marković',
                'username' => 'coordinator',
                'password' => Hash::make('password'),
                'role' => 'coordinator',
                'is_active' => true,
                'preferred_locale' => 'sr_Latn',
            ]
        );

        $employee = User::updateOrCreate(
            ['email' => 'marko.petrovic@rooms-demo.local'],
            [
                'name' => 'Marko Petrović',
                'username' => 'marko',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'is_active' => true,
                'preferred_locale' => 'sr_Latn',
            ]
        );

        // 2. Seed Settings
        $settings = [
            'time_step_minutes' => ['15', 'integer'],
            'min_duration_minutes' => ['15', 'integer'],
            'max_duration_minutes' => ['240', 'integer'],
            'max_days_ahead' => ['30', 'integer'],
            'office_email' => ['office@rooms-demo.local', 'string'],
            'office_timezone' => [$tz, 'string'],
            'show_company_calendar' => ['1', 'boolean'],
            'company_calendar_title_en' => ['Company Events & Town Halls', 'string'],
            'company_calendar_title_sr' => ['Događaji kompanije i sastanci timova', 'string'],
        ];

        foreach ($settings as $key => [$val, $type]) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $val, 'type' => $type]
            );
        }

        // 3. Seed Working Hours (1=Mon .. 7=Sun)
        $hoursConfig = [
            1 => [true, '08:00:00', '18:00:00'],
            2 => [true, '08:00:00', '18:00:00'],
            3 => [true, '08:00:00', '18:00:00'],
            4 => [true, '08:00:00', '18:00:00'],
            5 => [true, '08:00:00', '18:00:00'],
            6 => [false, '09:00:00', '15:00:00'],
            7 => [false, '09:00:00', '15:00:00'],
        ];

        foreach ($hoursConfig as $day => [$isOpen, $openTime, $closeTime]) {
            WorkingHour::updateOrCreate(
                ['day_of_week' => $day],
                ['is_open' => $isOpen, 'open_time' => $openTime, 'close_time' => $closeTime]
            );
        }

        // 4. Seed Holidays
        $year = $today->year;
        $holidays = [
            ["{$year}-01-01", "New Year's Day", 'Nova godina', true],
            ["{$year}-02-15", 'Statehood Day', 'Dan državnosti (Sretenje)', true],
            ["{$year}-11-11", 'Armistice Day', 'Dan primirja', true],
            [$today->copy()->addDays(10)->toDateString(), 'Company Innovation Day', 'Dan inovacija kompanije', false],
        ];

        foreach ($holidays as [$hDate, $nameEn, $nameSr, $recur]) {
            Holiday::updateOrCreate(
                ['holiday_date' => $hDate],
                ['name_en' => $nameEn, 'name_sr' => $nameSr, 'is_recurring' => $recur]
            );
        }

        // 5. Seed Company Events
        $events = [
            ['Q4 All-Hands Town Hall', 'Kvartalni sastanak svih zaposlenih', $today->toDateString(), '15:00:00', '16:30:00', 'Main Auditorium / Online'],
            ['Product Architecture Sync', 'Sinhronizacija arhitekture proizvoda', $today->copy()->addDay()->toDateString(), '11:00:00', '12:00:00', 'Boardroom Tesla / Hybrid'],
            ['Friday Office Social & Showcase', 'Petak druženje i prezentacije', $today->copy()->next(Carbon::FRIDAY)->toDateString(), '16:30:00', '18:00:00', 'Central Lounge'],
        ];

        foreach ($events as [$titleEn, $titleSr, $eDate, $sTime, $eTime, $loc]) {
            CompanyEvent::updateOrCreate(
                ['title_en' => $titleEn, 'event_date' => $eDate],
                [
                    'title_sr' => $titleSr,
                    'start_time' => $sTime,
                    'end_time' => $eTime,
                    'location' => $loc,
                ]
            );
        }

        // 6. Seed Rooms
        $roomsData = [
            [
                'slug' => 'nikola-tesla-boardroom',
                'name_en' => 'Nikola Tesla Boardroom',
                'name_sr' => 'Sala Nikola Tesla',
                'location' => 'Floor 3, North Wing / 3. sprat, Severno krilo',
                'capacity' => 16,
                'description_en' => 'Premier executive boardroom equipped with dual 4K displays, studio-grade video conferencing bar, and seating for 16.',
                'description_sr' => 'Vrhunska izvršna sala opremljena dvostrukim 4K ekranima, profesionalnim video sistemom i kapacitetom od 16 mesta.',
                'color_hex' => '#4F46E5',
                'photo_path' => 'rooms/nikola-tesla-boardroom.jpg',
                'is_active' => true,
                'sort_order' => 1,
                'equipments' => [
                    ['tv_4k', '4K TV Screen', '4K TV Ekran'],
                    ['video_conf', 'Video Conferencing', 'Video konferencijski sistem'],
                    ['whiteboard', 'Whiteboard', 'Bela tabla'],
                    ['speakerphone', 'Speakerphone', 'Konferencijski mikrofon'],
                    ['catering', 'Catering Allowed', 'Ketering dozvoljen'],
                ],
            ],
            [
                'slug' => 'mihajlo-pupin-lab',
                'name_en' => 'Mihajlo Pupin Innovation Lab',
                'name_sr' => 'Inovaciona sala Mihajlo Pupin',
                'location' => 'Floor 2, East Wing / 2. sprat, Istočno krilo',
                'capacity' => 10,
                'description_en' => 'Collaborative workshop space with interactive ultra-short throw projector and wall-to-wall magnetic glassboards.',
                'description_sr' => 'Prostor za radionice sa interaktivnim projektorom i magnetnim staklenim tablama duž celog zida.',
                'color_hex' => '#0D9488',
                'photo_path' => 'rooms/mihajlo-pupin-lab.jpg',
                'is_active' => true,
                'sort_order' => 2,
                'equipments' => [
                    ['projector', 'Ultra HD Projector', 'Ultra HD Projektor'],
                    ['whiteboard', 'Magnetic Glassboard', 'Magnetna tabla'],
                    ['hdmi_usbc', 'HDMI & USB-C Dock', 'HDMI & USB-C priključci'],
                    ['video_conf', 'Video Conferencing', 'Video konferencijski sistem'],
                ],
            ],
            [
                'slug' => 'milutin-milankovic',
                'name_en' => 'Milutin Milanković Conference Room',
                'name_sr' => 'Konferencijska sala Milutin Milanković',
                'location' => 'Floor 2, West Wing / 2. sprat, Zapadno krilo',
                'capacity' => 8,
                'description_en' => 'Mid-size meeting room tailored for project standups, sprint reviews, and client video presentations.',
                'description_sr' => 'Sala srednje veličine pogodna za timske sastanke, pregled sprinta i video prezentacije klijentima.',
                'color_hex' => '#2563EB',
                'photo_path' => 'rooms/milutin-milankovic.jpg',
                'is_active' => true,
                'sort_order' => 3,
                'equipments' => [
                    ['tv_4k', '4K TV Screen', '4K TV Ekran'],
                    ['whiteboard', 'Whiteboard', 'Bela tabla'],
                    ['hdmi_usbc', 'HDMI & USB-C', 'HDMI & USB-C'],
                ],
            ],
            [
                'slug' => 'avalon-focus-pod-a',
                'name_en' => 'Avalon Focus Pod A',
                'name_sr' => 'Avalon Fokus Sala A',
                'location' => 'Floor 1, Huddle Zone / 1. sprat, Zona za fokus',
                'capacity' => 4,
                'description_en' => 'Acoustically treated huddle pod designed for 1-on-1 reviews, interviews, and deep focus sessions.',
                'description_sr' => 'Akustično izolovana manja sala za intervjue, 1-na-1 sastanke i fokusiran timski rad.',
                'color_hex' => '#7C3AED',
                'photo_path' => 'rooms/avalon-focus-pod-a.jpg',
                'is_active' => true,
                'sort_order' => 4,
                'equipments' => [
                    ['tv_4k', '4K TV Screen', '4K TV Ekran'],
                    ['whiteboard', 'Whiteboard', 'Bela tabla'],
                ],
            ],
            [
                'slug' => 'danube-quick-sync',
                'name_en' => 'Danube Quick Sync Room',
                'name_sr' => 'Dunav Sala za brze sastanke',
                'location' => 'Floor 1, Central Lobby / 1. sprat, Centralni hol',
                'capacity' => 6,
                'description_en' => 'Fast-access room located near the main coffee hub for quick 15-to-30 minute coordination meetings.',
                'description_sr' => 'Brzo dostupna sala blizu kafea za kratke koordinacione sastanke od 15 do 30 minuta.',
                'color_hex' => '#D97706',
                'photo_path' => 'rooms/danube-quick-sync.jpg',
                'is_active' => true,
                'sort_order' => 5,
                'equipments' => [
                    ['whiteboard', 'Whiteboard', 'Bela tabla'],
                    ['speakerphone', 'Speakerphone', 'Konferencijski mikrofon'],
                ],
            ],
            [
                'slug' => 'executive-lounge-vip',
                'name_en' => 'Executive Lounge (VIP Hidden)',
                'name_sr' => 'Izvršni salon (Skriveno)',
                'location' => 'Floor 4, Penthouse Suite / 4. sprat',
                'capacity' => 12,
                'description_en' => 'Private VIP suite reserved exclusively for board directors and confidential negotiations.',
                'description_sr' => 'Privatni VIP salon rezervisan isključivo za upravni odbor i poverljive pregovore.',
                'color_hex' => '#64748B',
                'photo_path' => 'rooms/executive-lounge-vip.jpg',
                'is_active' => false,
                'sort_order' => 6,
                'equipments' => [
                    ['tv_4k', '4K TV Screen', '4K TV Ekran'],
                    ['video_conf', 'Video Conferencing', 'Video konferencijski sistem'],
                    ['catering', 'Catering Allowed', 'Ketering dozvoljen'],
                ],
            ],
        ];

        $rooms = [];
        foreach ($roomsData as $r) {
            $equipments = $r['equipments'];
            unset($r['equipments']);

            $room = Room::updateOrCreate(['slug' => $r['slug']], $r);
            $rooms[$room->slug] = $room;

            RoomEquipment::where('room_id', $room->id)->delete();
            foreach ($equipments as [$code, $labelEn, $labelSr]) {
                RoomEquipment::create([
                    'room_id' => $room->id,
                    'code' => $code,
                    'label_en' => $labelEn,
                    'label_sr' => $labelSr,
                ]);
            }
        }

        // 7. Seed Sample Bookings (18 bookings total, including Today, Back-to-Back, Tomorrow, Next Week, Cancelled)
        // Clean old sample bookings to avoid seed duplication
        Booking::query()->delete();
        SimulatedEmail::query()->delete();

        $calendarService = new CalendarLinkService();
        $emailDelivery = new SimulatedEmailDeliveryService(new \App\Services\SettingService(), $calendarService);

        // Target active workday for "Today" bookings (if today is weekend, use next Monday)
        $workdayToday = $today->isWeekend() ? $today->copy()->next(Carbon::MONDAY) : $today->copy();
        $workdayTomorrow = $workdayToday->copy()->addDay()->isWeekend() ? $workdayToday->copy()->next(Carbon::MONDAY) : $workdayToday->copy()->addDay();
        $workdayLater = $workdayToday->copy()->addDays(2)->isWeekend() ? $workdayToday->copy()->next(Carbon::TUESDAY) : $workdayToday->copy()->addDays(2);
        $workdayNextWeek = $workdayToday->copy()->addDays(7);

        $bookingsPlan = [
            // Today - Room 1 (Back-to-Back pair!)
            [
                'room' => $rooms['nikola-tesla-boardroom'],
                'date' => $workdayToday,
                'start' => '10:00',
                'duration' => 60,
                'title' => 'Client Roadmap & Q4 Milestone Presentation',
                'agenda' => 'Presenting the upcoming product deliverables to regional key stakeholders.',
                'name' => 'Marko Petrović',
                'email' => 'marko.petrovic@rooms-demo.local',
                'attendees' => 8,
                'status' => 'confirmed',
                'user' => $employee,
            ],
            [
                'room' => $rooms['nikola-tesla-boardroom'],
                'date' => $workdayToday,
                'start' => '11:00', // Back-to-Back right after 10:00-11:00!
                'duration' => 60,
                'title' => 'Executive Debrief & Resource Planning',
                'agenda' => 'Internal discussion following the client presentation.',
                'name' => 'Nikola Jovanović',
                'email' => 'admin@rooms-demo.local',
                'attendees' => 5,
                'status' => 'confirmed',
                'user' => $admin,
            ],
            // Today - Room 2
            [
                'room' => $rooms['mihajlo-pupin-lab'],
                'date' => $workdayToday,
                'start' => '09:00',
                'duration' => 90,
                'title' => 'Mobile Engineering Sprint Review & Demo',
                'agenda' => 'Reviewing responsive design deliverables and headless testing audit scores.',
                'name' => 'Ana Marković',
                'email' => 'coordinator@rooms-demo.local',
                'attendees' => 7,
                'status' => 'confirmed',
                'user' => $coordinator,
            ],
            // Today - Room 3
            [
                'room' => $rooms['milutin-milankovic'],
                'date' => $workdayToday,
                'start' => '13:00',
                'duration' => 60,
                'title' => 'Legal & Workspace Compliance Briefing',
                'agenda' => 'Quarterly review of office policies and contractor NDA protocols.',
                'name' => 'Stefan Đorđević',
                'email' => 'stefan.djordjevic@example.com',
                'attendees' => 4,
                'status' => 'confirmed',
                'user' => null,
            ],
            // Today - Room 4
            [
                'room' => $rooms['avalon-focus-pod-a'],
                'date' => $workdayToday,
                'start' => '14:30',
                'duration' => 60,
                'title' => 'Bi-Weekly Mentorship & Goals 1-on-1',
                'agenda' => 'Career development checkpoints and performance review.',
                'name' => 'Jelena Nikolić',
                'email' => 'jelena.nikolic@example.com',
                'attendees' => 2,
                'status' => 'confirmed',
                'user' => null,
            ],
            // Today - Room 5
            [
                'room' => $rooms['danube-quick-sync'],
                'date' => $workdayToday,
                'start' => '10:00',
                'duration' => 45,
                'title' => 'Budget Reallocation Fast Sync',
                'agenda' => 'Rapid alignment on hardware upgrade expenses.',
                'name' => 'Marko Petrović',
                'email' => 'marko.petrovic@rooms-demo.local',
                'attendees' => 4,
                'status' => 'confirmed',
                'user' => $employee,
            ],
            // Tomorrow Bookings
            [
                'room' => $rooms['nikola-tesla-boardroom'],
                'date' => $workdayTomorrow,
                'start' => '09:30',
                'duration' => 90,
                'title' => 'Investor Pitch Rehearsal',
                'agenda' => 'Dry run of presentation slides and financial models.',
                'name' => 'Nikola Jovanović',
                'email' => 'admin@rooms-demo.local',
                'attendees' => 12,
                'status' => 'confirmed',
                'user' => $admin,
            ],
            [
                'room' => $rooms['mihajlo-pupin-lab'],
                'date' => $workdayTomorrow,
                'start' => '14:00',
                'duration' => 120,
                'title' => 'Design System & Preline UI Workshop',
                'agenda' => 'Hands-on tokens refactoring and Tailwind CSS v4 exploration.',
                'name' => 'Ana Marković',
                'email' => 'coordinator@rooms-demo.local',
                'attendees' => 8,
                'status' => 'confirmed',
                'user' => $coordinator,
            ],
            [
                'room' => $rooms['milutin-milankovic'],
                'date' => $workdayTomorrow,
                'start' => '10:00',
                'duration' => 60,
                'title' => 'Vendor Security Questionnaire Audit',
                'agenda' => 'ISO 27001 readiness review.',
                'name' => 'Vladimir Lukić',
                'email' => 'vladimir.lukic@example.com',
                'attendees' => 6,
                'status' => 'confirmed',
                'user' => null,
            ],
            // Later This Week
            [
                'room' => $rooms['avalon-focus-pod-a'],
                'date' => $workdayLater,
                'start' => '11:00',
                'duration' => 30,
                'title' => 'Candidate Technical Interview (Stage 2)',
                'agenda' => 'Live coding review and systems architecture discussion.',
                'name' => 'Marko Petrović',
                'email' => 'marko.petrovic@rooms-demo.local',
                'attendees' => 3,
                'status' => 'confirmed',
                'user' => $employee,
            ],
            [
                'room' => $rooms['danube-quick-sync'],
                'date' => $workdayLater,
                'start' => '15:00',
                'duration' => 60,
                'title' => 'Marketing Campaign Retrospective',
                'agenda' => 'Analytics review and learnings from October product launch.',
                'name' => 'Maja Simić',
                'email' => 'maja.simic@example.com',
                'attendees' => 5,
                'status' => 'confirmed',
                'user' => null,
            ],
            // Next Week Bookings
            [
                'room' => $rooms['nikola-tesla-boardroom'],
                'date' => $workdayNextWeek,
                'start' => '10:00',
                'duration' => 120,
                'title' => 'Board of Directors Strategy Assembly',
                'agenda' => 'Annual strategic objectives and capital budget allocation.',
                'name' => 'Nikola Jovanović',
                'email' => 'admin@rooms-demo.local',
                'attendees' => 14,
                'status' => 'confirmed',
                'user' => $admin,
            ],
            [
                'room' => $rooms['mihajlo-pupin-lab'],
                'date' => $workdayNextWeek,
                'start' => '13:00',
                'duration' => 90,
                'title' => 'Cross-Department AI Tooling Exploration',
                'agenda' => 'Demonstrating automated QA auditor workflows to product teams.',
                'name' => 'Marko Petrović',
                'email' => 'marko.petrovic@rooms-demo.local',
                'attendees' => 9,
                'status' => 'confirmed',
                'user' => $employee,
            ],
            [
                'room' => $rooms['milutin-milankovic'],
                'date' => $workdayNextWeek,
                'start' => '09:00',
                'duration' => 60,
                'title' => 'Client Kickoff: Adanix Workspace Cloud',
                'agenda' => 'Introducing teams and agreeing on milestone deadlines.',
                'name' => 'Ana Marković',
                'email' => 'coordinator@rooms-demo.local',
                'attendees' => 7,
                'status' => 'confirmed',
                'user' => $coordinator,
            ],
            [
                'room' => $rooms['avalon-focus-pod-a'],
                'date' => $workdayNextWeek,
                'start' => '16:00',
                'duration' => 30,
                'title' => 'Salary Review & Promotion Checkpoint',
                'agenda' => 'Private quarterly review meeting.',
                'name' => 'Nikola Jovanović',
                'email' => 'admin@rooms-demo.local',
                'attendees' => 2,
                'status' => 'confirmed',
                'user' => $admin,
            ],
            [
                'room' => $rooms['danube-quick-sync'],
                'date' => $workdayNextWeek,
                'start' => '11:15',
                'duration' => 45,
                'title' => 'HR Wellbeing Initiative Sync',
                'agenda' => 'Planning winter team building activities.',
                'name' => 'Milica Božić',
                'email' => 'milica.bozic@example.com',
                'attendees' => 4,
                'status' => 'confirmed',
                'user' => null,
            ],
            // 2 Cancelled Bookings (for testing Restore in Admin Panel)
            [
                'room' => $rooms['nikola-tesla-boardroom'],
                'date' => $workdayTomorrow,
                'start' => '16:00',
                'duration' => 60,
                'title' => 'Cancelled: International Logistics Sync',
                'agenda' => 'Postponed due to flight delay of client delegation.',
                'name' => 'Marko Petrović',
                'email' => 'marko.petrovic@rooms-demo.local',
                'attendees' => 6,
                'status' => 'cancelled',
                'user' => $employee,
                'cancellation_reason' => 'Client flight delayed until next week.',
            ],
            [
                'room' => $rooms['milutin-milankovic'],
                'date' => $workdayLater,
                'start' => '14:00',
                'duration' => 60,
                'title' => 'Cancelled: Quarterly Hardware Purchasing Review',
                'agenda' => 'Meeting cancelled because budget proposal was approved asynchronously.',
                'name' => 'Ana Marković',
                'email' => 'coordinator@rooms-demo.local',
                'attendees' => 4,
                'status' => 'cancelled',
                'user' => $coordinator,
                'cancellation_reason' => 'Approved via email; in-person discussion not needed.',
            ],
        ];

        foreach ($bookingsPlan as $i => $bp) {
            $dateStr = $bp['date']->toDateString();
            $startAt = Carbon::parse("{$dateStr} {$bp['start']}", $tz);
            $endAt = $startAt->copy()->addMinutes($bp['duration']);
            $ref = 'RM-' . $bp['date']->format('Ymd') . '-' . sprintf('%04d', $i + 1);
            $token = bin2hex(random_bytes(24));

            $booking = Booking::create([
                'reference_code' => $ref,
                'management_token' => $token,
                'room_id' => $bp['room']->id,
                'created_by_user_id' => $bp['user']?->id,
                'booking_date' => $bp['date']->toDateString(),
                'start_at' => $startAt,
                'end_at' => $endAt,
                'duration_minutes' => $bp['duration'],
                'title' => $bp['title'],
                'agenda' => $bp['agenda'],
                'organizer_name' => $bp['name'],
                'organizer_email' => $bp['email'],
                'attendees_count' => $bp['attendees'],
                'status' => $bp['status'],
                'locale' => 'en',
                'cancellation_reason' => $bp['cancellation_reason'] ?? null,
                'cancelled_at' => ($bp['status'] === 'cancelled') ? $today->copy()->subHours(2) : null,
                'cancelled_by' => ($bp['status'] === 'cancelled') ? 'organizer' : null,
            ]);

            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'actor_user_id' => $bp['user']?->id,
                'action' => $bp['status'] === 'cancelled' ? 'cancelled' : 'created',
                'new_state' => $booking->toArray(),
                'note' => $bp['status'] === 'cancelled' ? 'Initial demo cancelled booking' : 'Initial demo booking',
                'created_at' => $today->copy()->subHours(4),
            ]);

            // Dispatch simulated emails so Outbox is populated
            if ($bp['status'] === 'cancelled') {
                $emailDelivery->sendBookingCancelled($booking);
            } else {
                $emailDelivery->sendBookingCreated($booking);
            }
        }
    }
}
