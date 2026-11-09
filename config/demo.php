<?php

return [
    'enabled' => (bool) env('APP_DEMO_MODE', true),
    'contact_email' => env('DEMO_CONTACT_EMAIL', 'nixresponding@gmail.com'),
    'office_email' => env('DEMO_OFFICE_EMAIL', 'office@rooms-demo.local'),
    'personas' => [
        'admin' => [
            'email' => 'admin@rooms-demo.local',
            'name' => 'Nikola Jovanović',
            'role' => 'admin',
            'redirect_route' => 'admin.dashboard',
        ],
        'coordinator' => [
            'email' => 'coordinator@rooms-demo.local',
            'name' => 'Ana Marković',
            'role' => 'coordinator',
            'redirect_route' => 'admin.bookings.index',
        ],
        'employee' => [
            'email' => 'marko.petrovic@rooms-demo.local',
            'name' => 'Marko Petrović',
            'role' => 'employee',
            'redirect_route' => 'rooms.index',
        ],
    ],
];
