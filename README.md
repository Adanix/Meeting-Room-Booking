# ROOMS — Enterprise Meeting Room Booking & Workspace Scheduling System

[![Laravel 13](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP 8.5](https://img.shields.io/badge/PHP-8.5-777BB4?style=flat&logo=php)](https://php.net)
[![Tailwind CSS v4](https://img.shields.io/badge/TailwindCSS-v4.x-38B2AC?style=flat&logo=tailwind-css)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=flat&logo=alpine.js)](https://alpinejs.dev)
[![MySQL 8](https://img.shields.io/badge/MySQL-8.x-4479A1?style=flat&logo=mysql)](https://mysql.com)
[![Automated Tests](https://img.shields.io/badge/Tests-33%20Passed%20(165%20assertions)-emerald?style=flat&logo=checkmarx)](#automated-testing--quality-assurance)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

An enterprise-grade, conflict-free meeting room reservation and workspace scheduling application built for corporate offices, innovation hubs, co-working spaces, and hybrid workforces. Engineered with a mathematically proven half-open interval engine, self-service rescheduling, calendar synchronization, bilingual localization (English & Serbian Latin), and a complete facilities administration suite.

---

## Customization & Enterprise Inquiries

> **Need a custom workspace scheduling solution?**  
> This application was built to specification by **[nixresponding@gmail.com](mailto:nixresponding@gmail.com)**.  
> We design and deliver custom software systems tailored precisely to your operational workflows, organizational structure, internal branding, and security compliance requirements.  
> Reach out to discuss custom features, Microsoft 365 / Google Workspace calendar two-way synchronization, RFID/NFC door access badge reader integrations, single sign-on (SSO/SAML), on-premise deployments, or dedicated SLA support.

---

## Table of Contents

- [Key Features](#key-features)
- [System Architecture & Tech Stack](#system-architecture--tech-stack)
- [Demo Mode & Predefined Personas](#demo-mode--predefined-personas)
- [Core Business Logic & Algorithms](#core-business-logic--algorithms)
- [Database Schema & Domain Models](#database-schema--domain-models)
- [API Endpoints Reference](#api-endpoints-reference)
- [Installation & Quickstart](#installation--quickstart)
- [Automated Testing & Quality Assurance](#automated-testing--quality-assurance)
- [Project Structure](#project-structure)
- [License](#license)

---

## Key Features

### 1. Conflict-Free Booking Engine
- **Half-Open Intervals:** All bookings are evaluated on $[t_{\text{start}}, t_{\text{end}})$, guaranteeing that contiguous back-to-back meetings (e.g., 10:00–11:00 and 11:00–12:00) resolve cleanly without overlap errors.
- **Pessimistic Concurrency Locking:** Employs `lockForUpdate()` within atomic database transactions to serialize simultaneous reservation attempts for the same room.
- **Operating Hours & Holiday Rules:** Validates opening/closing schedules per day-of-week and strictly blocks reservations on national holidays or company shutdown days.

### 2. Public Room Portal & Real-Time Availability
- **Live Room Cards:** Real-time visual status pills:
  - <span style="color:#10b981;font-weight:bold">Available Now</span> (emerald)
  - <span style="color:#f59e0b;font-weight:bold">Busy / Occupied</span> (amber, with countdown until free)
  - <span style="color:#64748b;font-weight:bold">Facility Closed</span> (slate, outside operating hours or during holidays)
- **Dynamic Search & Filtering:** Filter rooms by minimum attendee capacity, amenities (4K TV, Projector, Video Conferencing Bar, Magnetic Whiteboard, Speakerphone, Catering), and target date.
- **Daily Schedule Timeline:** Chronological timeline view displaying room allocation blocks across operating hours.

### 3. Self-Service Management & Cutoff Enforcement
- **Unguessable Access Tokens:** Every reservation generates a secure 64-character unguessable URL token sent in confirmation emails.
- **Self-Service Actions:** Employees can reschedule meeting times or cancel reservations directly without administrator intervention.
- **Strict Elapsed Cutoff Rule:** Past meetings and reservations where the start time has already arrived are permanently locked against modifications or cancellations.

### 4. Calendar Sync & Integrations
- **1-Click Google Calendar:** Instant URL generation with pre-populated meeting title, description, room location, and ISO timestamps.
- **Native RFC 5545 iCalendar (`.ics`):** Clean export compatible with Microsoft Outlook, Apple Calendar, Google Calendar, and mobile calendar apps.

### 5. Facilities Administration Suite
- **Operational Dashboard:** Real-time metrics including total bookings, facility utilization rates, today's schedule, and room status overview.
- **7-Column Monthly Calendar Grid:** Visual Monday-first month calendar featuring color-coded meeting blocks, holiday chips, company event badges, and a day drill-down modal.
- **Bookings Management:** Filterable data table with instant search, inline status toggle, cancellation with audit notes, and restoration capabilities.
- **Excel-Compatible CSV Export:** Exports full booking histories with UTF-8 BOM (`\xEF\xBB\xBF`) ensuring special characters (including Serbian Latin `č, ć, ž, š, đ`) open cleanly in Microsoft Excel.
- **Room CRUD & Photography:** Full management of room profiles, capacities, equipment chips, and photo uploads with local storage symlink synchronization.
- **Global Settings:** Centralized configuration of slot intervals (15/30/60m), min/max meeting durations, advance booking horizons, working hours matrix, and holidays.

### 6. Interactive Simulated Email Outbox
- **Zero External SMTP Requirement:** In Demo Mode, all transaction emails (booking confirmations, reschedule notices, cancellation receipts, admin alerts) are captured and stored in the database.
- **Slide-Over Drawer & Admin View:** View simulated emails directly via the topbar drawer button or `/admin/emails` with search, event category pills, and sandboxed HTML iframe previews.

### 7. Bilingual Localization (EN & SR-Latn)
- Seamless dual-language support for **English (`en`)** and **Serbian Latin (`sr_Latn`)**.
- Localized room descriptions, equipment names, email notifications, and ISO dates via Carbon.
- Session-persisted language toggle accessible on all navigation bars.

---

## System Architecture & Tech Stack

```
┌────────────────────────────────────────────────────────┐
│                   Tailwind CSS v4 + Alpine.js          │
│                Laravel 13 Blade Architecture           │
└───────────────────────────┬────────────────────────────┘
                            │
┌───────────────────────────▼────────────────────────────┐
│                    HTTP Middleware                     │
│          • SetLocale        • RoleMiddleware           │
│          • DemoModeGate     • Authenticate             │
└───────────────────────────┬────────────────────────────┘
                            │
┌───────────────────────────▼────────────────────────────┐
│                  Service Layer                         │
│   • AvailabilityService     • CalendarLinkService      │
│   • IcsGeneratorService     • SimulatedEmailService    │
└───────────────────────────┬────────────────────────────┘
                            │
┌───────────────────────────▼────────────────────────────┐
│              Database & Storage Engine                 │
│      MySQL 8.x (InnoDB, Transactions, Row-Locking)     │
│             Laravel Public Storage Disk                │
└────────────────────────────────────────────────────────┘
```

- **Backend:** Laravel 13.x running on PHP 8.5
- **Frontend:** Laravel Blade, Tailwind CSS v4, Alpine.js, Lucide Icons
- **Database:** MySQL 8.0 (InnoDB)
- **Asset Pipeline:** Vite
- **Testing:** PHPUnit / Pest-compatible Laravel Feature & Unit test suites

---

## Demo Mode & Predefined Personas

When running with `APP_DEMO_MODE=true`, authentication requires no password entry. Users can log in instantly via the Demo Persona Switcher (`/demo/login`) or the persistent Demo Topbar:

| Persona | Role | Email | Direct Landing | Permissions |
|---|---|---|---|---|
| **Nikola Jovanović** | Facility Administrator (`admin`) | `admin@rooms-demo.local` | `/admin/dashboard` | Full control: room CRUD, settings, working hours, holidays, bookings CRUD, CSV export, simulated email drawer. |
| **Ana Marković** | Facility Coordinator (`coordinator`) | `coordinator@rooms-demo.local` | `/admin/bookings` | Operational management: calendar grid, bookings list, reschedule, cancel/restore, CSV export. |
| **Marko Petrović** | Corporate Employee (`employee`) | `marko.petrovic@rooms-demo.local` | `/rooms` | Public room portal, conflict-free booking flow, self-service token link, `.ics` calendar sync. |

*Default password for standard `/login`: `password` or `demo1234`.*

---

## Core Business Logic & Algorithms

### 1. Conflict-Free Booking Math
Given an existing booking interval $A = [s_A, e_A)$ and a requested booking interval $B = [s_B, e_B)$, a collision occurs if and only if:
$$\text{Overlap} \iff s_A < e_B \quad \text{AND} \quad e_A > s_B$$

Contiguous bookings where $e_A = s_B$ evaluate to `false` and are permitted.

### 2. Concurrency Serialization
Simultaneous reservation requests for the same room are wrapped inside an atomic transaction:
```php
DB::transaction(function () use ($roomId, $start, $end) {
    // Acquire pessimistic row lock
    $room = Room::whereKey($roomId)->lockForUpdate()->firstOrFail();
    
    // Check for conflicting overlapping bookings
    $conflict = Booking::where('room_id', $room->id)
        ->whereIn('status', ['confirmed', 'pending'])
        ->where('start_at', '<', $end)
        ->where('end_at', '>', $start)
        ->exists();
        
    if ($conflict) {
        throw new BookingConflictException();
    }
    
    // Create confirmed reservation
    return Booking::create([...]);
});
```

### 3. Elapsed Meeting Cutoff Rule
Self-service modifications and cancellations check the meeting timestamp against the facility's localized current time:
```php
if (Carbon::now($timezone)->gte($booking->start_at)) {
    return back()->with('error', 'Past meetings cannot be modified or cancelled.');
}
```

---

## Database Schema & Domain Models

- **`rooms`**: Physical space metadata (`name_en`, `name_sr`, `slug`, `capacity`, `color_hex`, `photo_path`, `is_active`, `sort_order`).
- **`room_equipments`**: Amenities attached to rooms (`equipment_key`, `name_en`, `name_sr`, `icon`).
- **`bookings`**: Reservations (`reference`, `token`, `user_id`, `room_id`, `title`, `description`, `start_at`, `end_at`, `attendees_count`, `organizer_name`, `organizer_email`, `status`).
- **`booking_status_histories`**: Audit log of all state transitions (`booking_id`, `from_status`, `to_status`, `changed_by`, `notes`).
- **`working_hours`**: Day-of-week operating hours (`day_of_week`, `is_open`, `open_time`, `close_time`).
- **`holidays`**: Closed dates and company holidays (`holiday_date`, `name_en`, `name_sr`, `is_recurring`).
- **`company_events`**: Internal events displayed on calendar (`title_en`, `title_sr`, `event_date`, `start_time`, `end_time`, `location`).
- **`settings`**: Key-value configuration store (`time_step_minutes`, `min_duration_minutes`, `max_duration_minutes`, `max_days_ahead`, `office_email`, `office_timezone`).
- **`simulated_emails`**: In-app SMTP intercept store (`to_email`, `subject`, `body_html`, `event_type`, `headers_json`).

---

## API Endpoints Reference

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/rooms/{room}/available-slots?date=YYYY-MM-DD` | Returns JSON array of available time intervals for the specified room and date. |
| `POST` | `/locale` | Switches active user session locale (`en` or `sr_Latn`). |
| `GET` | `/bookings/{reference}/ics` | Downloads native RFC 5545 `.ics` iCalendar file for confirmed reservation. |
| `GET` | `/bookings/manage/{token}` | Self-service management interface for meeting organizer. |
| `POST` | `/bookings/manage/{token}/reschedule` | Self-service reschedule endpoint. |
| `POST` | `/bookings/manage/{token}/cancel` | Self-service cancellation endpoint. |
| `GET` | `/admin/bookings/export` | Generates UTF-8 BOM Excel-compatible CSV export of reservations. |
| `POST` | `/demo/quick-login` | Authenticates persona instantly in demo mode. |
| `POST` | `/demo/reset-data` | Resets database tables and seeds factory demo state. |

---

## Installation & Quickstart

### Prerequisites
- PHP 8.2 or higher (PHP 8.5 recommended)
- Composer 2.x
- Node.js 20+ & npm
- MySQL 8.0+

### Step-by-Step Local Setup

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/Adanix/Meeting-Room-Booking.git
   cd Meeting-Room-Booking
   ```

2. **Install Composer Dependencies:**
   ```bash
   composer install
   ```

3. **Install & Build Frontend Assets:**
   ```bash
   npm install
   npm run build
   ```

4. **Configure Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure Database:**
   Edit `.env` with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=room_booking
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   
   APP_DEMO_MODE=true
   ```

6. **Run Migrations & Seed Demo Data:**
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Create Storage Symlink:**
   ```bash
   php artisan storage:link
   ```

8. **Start Local Development Server:**
   ```bash
   php artisan serve
   ```
   Open `http://localhost:8000` in your browser.

---

## Automated Testing & Quality Assurance

The application includes an extensive test suite verifying conflict detection, role permissions, bilingual emails, self-service cutoffs, and calendar exports.

```bash
php artisan test
```

### Test Suite Summary:
```text
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\AdminManagementAndExportTest
  ✓ guest is redirected away from admin dashboard
  ✓ employee role receives 403 forbidden on admin
  ✓ coordinator and admin can access admin panel
  ✓ csv export includes utf8 bom and headers
  ✓ admin can cancel and restore booking
  ✓ admin can toggle room portal visibility

   PASS  Tests\Feature\BilingualLocalizationTest
  ✓ switching locale via post endpoint
  ✓ serbian latin renders on rooms portal
  ✓ english renders on rooms portal
  ✓ bilingual simulated email dispatch

   PASS  Tests\Feature\BookingCreationAndRulesTest
  ✓ booking creation succeeds and dispatches emails and history
  ✓ back to back booking succeeds without conflict
  ✓ overlapping booking is rejected
  ✓ capacity exceeded is rejected
  ✓ booking on holiday is rejected

   PASS  Tests\Feature\ConfirmationAndCalendarTest
  ✓ booking confirmation page renders details and action links
  ✓ ics download returns valid rfc5545 calendar

   PASS  Tests\Feature\P0PublicAndDemoAuthTest
  ✓ p0 01 homepage returns 200 and renders brand and customization notice
  ✓ p0 02 docs page returns 200 and contains customization notice
  ✓ p0 03 demo login page renders three personas
  ✓ p0 04 quick login authenticates each persona and redirects
  ✓ p0 05 logout invalidates session and redirects
  ✓ p0 06 demo routes return 404 when demo mode disabled

   PASS  Tests\Feature\RoomStatusAndSearchTest
  ✓ room portal displays active rooms
  ✓ hidden rooms are not displayed on public portal
  ✓ filtering rooms by capacity
  ✓ filtering rooms by equipment
  ✓ available slots api returns json array of slots

   PASS  Tests\Feature\SelfServiceManageBookingTest
  ✓ self service page renders for valid token
  ✓ self service can reschedule future booking
  ✓ self service can cancel future booking
  ✓ self service cutoff rejects past meeting changes

  Tests:    33 passed (165 assertions)
  Duration: 7.10s
```

---

## Project Structure

```
Meeting-Room-Booking/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Admin, Booking, Demo, Public, Schedule controllers
│   │   └── Middleware/        # DemoModeGate, RoleMiddleware, SetLocale
│   ├── Models/                # Room, Booking, SimulatedEmail, Setting, etc.
│   └── Services/              # Availability, CalendarLink, IcsGenerator, etc.
├── database/
│   ├── migrations/            # Schema migrations
│   └── seeders/               # DatabaseSeeder with synced demo photography
├── resources/
│   ├── css/                   # Tailwind CSS v4 styling
│   ├── js/                    # Application scripts
│   └── views/
│       ├── admin/             # Facilities management, calendar grid, settings
│       ├── components/        # Blade components (email drawer, topbar, modal)
│       └── pages/             # Landing, rooms catalog, booking wizard, schedule
├── routes/
│   ├── api.php                # Slot availability endpoints
│   └── web.php                # Public, admin, and demo routes
└── tests/
    └── Feature/               # 7 complete feature test suites
```

---

## License

This project is open-source software licensed under the [MIT License](LICENSE).
