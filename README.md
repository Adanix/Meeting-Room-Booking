# ROOMS — Enterprise Meeting Room Booking & Workspace Scheduling System

**Live Public Demo:** [https://room-booking.adanix.my.id](https://room-booking.adanix.my.id)  
**Technical Documentation:** [https://room-booking.adanix.my.id/docs](https://room-booking.adanix.my.id/docs)  
**Demo Persona Switcher:** [https://room-booking.adanix.my.id/demo/login](https://room-booking.adanix.my.id/demo/login)

---

## Customization Notice

> **Need a custom workspace scheduling solution?**  
> This demo application was built to specification by **[nixresponding@gmail.com](mailto:nixresponding@gmail.com)**.  
> We design and deliver custom software systems tailored precisely to your operational workflow, organizational structure, internal branding, and security compliance needs.  
> Reach out to discuss custom features, Microsoft 365 / Google Workspace calendar two-way synchronization, RFID/NFC door access badge reader integrations, single sign-on (SSO/SAML), on-premise deployments, or dedicated SLA support.

---

## 1. Demo Mode & Quick Login Personas

When running in Demo Mode (`APP_DEMO_MODE=true`), authentication requires zero password typing. Access `/demo/login` or click the persistent **Demo Topbar** to switch instantly between 3 personas:

| Persona | Role | Email | Direct Landing Page | Access Permissions |
|---|---|---|---|---|
| **Nikola Jovanović** | Facility Administrator (`admin`) | `admin@rooms-demo.local` | `/admin/dashboard` | Full control: room CRUD, photo upload, settings, hours, holidays, bookings CRUD, CSV export, email drawer. |
| **Ana Marković** | Facility Coordinator (`coordinator`) | `coordinator@rooms-demo.local` | `/admin/bookings` | Operational management: calendar grid, bookings list, reschedule, cancel/restore, CSV export. |
| **Marko Petrović** | Corporate Employee (`employee`) | `marko.petrovic@rooms-demo.local` | `/rooms` | Public room portal, conflict-free booking flow, self-service token link, `.ics` calendar sync. |

*Default password for standard `/login`: `demo1234`.*

---

## 2. Key Modules & Functional Architecture

### A. Conflict-Free Booking Engine & Overlap Math
- All slot intervals are modeled as half-open: `[start_at, end_at)`.
- Two bookings $A$ and $B$ collide if and only if:  
  $$\text{start}_A < \text{end}_B \quad \text{AND} \quad \text{end}_A > \text{start}_B$$
- Back-to-back bookings (e.g. 10:00–11:00 and 11:00–12:00) evaluate to `false` on overlap, succeeding without conflict.
- Database-level concurrency protection utilizes `Room::whereKey($id)->lockForUpdate()` inside `DB::transaction()` to serialize simultaneous reservation requests.

### B. Public Room Portal (`/rooms`) & Availability Matrix
- Real-time status indicators: **Available Now** (emerald), **Busy / Occupied** (amber with remaining time countdown), and **Facility Closed** (slate).
- Real-time filters by minimum capacity, amenities (4K TV, Projector, Video Conferencing, Whiteboard, Speakerphone, Catering), and target date.
- Dynamic slot picker endpoint: `GET /api/rooms/{room}/available-slots` returning 15/30/60m increments.

### C. Self-Service Management & Cutoff Rule (`/bookings/manage/{token}`)
- Secure 64-character unguessable URL token sent in confirmation emails.
- Allows employees to reschedule or cancel reservations without coordinator intervention.
- **Strict Cutoff Rule:** Modification and cancellation are blocked once the meeting start timestamp has elapsed.

### D. Calendar Sync & Integrations
- One-click Google Calendar URL generator with pre-filled title, room location, and encoded timestamps.
- Native RFC 5545 `.ics` iCalendar download with clean UID, VEVENT sequence, and localized descriptions.

### E. Facilities Admin Panel (`/admin/*`)
- KPI dashboard with total reservations, utilization rates, and active room counters.
- 7-column monthly visual calendar grid with room color pills, day drill-down modal, and month navigator.
- Excel-compatible CSV export (`/admin/bookings/export`) with UTF-8 BOM (`\xEF\xBB\xBF`) ensuring Serbian Latin characters (`č, ć, ž, š, đ`) display cleanly in MS Excel.
- Full Room CRUD with photo file uploads (max 2MB WebP/JPG/PNG) stored in public storage symlink.
- Global facility working hours (Mon–Sun open/close times), company holidays, and company events configuration.

### F. Simulated SMTP Email Outbox (`<x-email-outbox-drawer />` & `/admin/emails`)
- In Demo Mode, all transaction emails (Booking Confirmation, Rescheduled, Cancelled, Restored) to employees and facilities office (`office@rooms-demo.local`) are intercepted and stored in MySQL table `simulated_emails`.
- Accessible via the topbar drawer button or `/admin/emails` with search, event pills, and sandboxed HTML iframe preview.

### G. Bilingual Localization
- Dual language support: English (`en`) and Serbian Latin (`sr_Latn`).
- Session-persisted switcher via `POST /locale`.
- Fully localized dates (Carbon ISO format), room titles, status badges, and transaction email bodies.

---

## 3. Technology Stack

- **Backend Framework:** Laravel 13.x (PHP 8.5)
- **Database Engine:** MySQL 8.x
- **Frontend Stack:** Laravel Blade + Tailwind CSS v4 + Alpine.js
- **Routing & Ingress:** Cloudflare Tunnel (`homelab`) + Nginx Proxy Manager (Port 8808)
- **Container Infrastructure:** Docker / Docker Compose via Adanix Project Factory

---

## 4. Automated Testing Suite

The application includes a comprehensive test suite covering all PRD specifications:

```bash
# Execute automated test suite inside Docker container
docker exec room-booking-app-1 php artisan test
```

### Test Coverage Highlights:
- `P0PublicAndDemoAuthTest`: Verifies public routes, docs mailto notice, 3-persona demo authentication, session regeneration, and demo mode disable gate.
- `RoomStatusAndSearchTest`: Verifies active/hidden room visibility, capacity and equipment filters, and JSON slot availability endpoint.
- `BookingCreationAndRulesTest`: Verifies half-open interval math, back-to-back success, overlap rejection, capacity limits, and national holiday closures.
- `ConfirmationAndCalendarTest`: Verifies confirmation screen, Google Calendar link syntax, and RFC 5545 `.ics` generator format.
- `SelfServiceManageBookingTest`: Verifies token-based self-service reschedule, cancellation, and past meeting cutoff rule.
- `BilingualLocalizationTest`: Verifies language switching, localized Blade strings, and Serbian email rendering.
- `AdminManagementAndExportTest`: Verifies role-based access control (403 for employees), Excel UTF-8 BOM CSV export, and room visibility toggling.

---

## 5. Maintenance & Resetting Demo Data

To reset the database back to factory demo state at any time:
- Click **"Reset Demo Data"** in the topbar banner, OR
- Run inside container:
  ```bash
  docker exec room-booking-app-1 php artisan migrate:fresh --seed
  ```
