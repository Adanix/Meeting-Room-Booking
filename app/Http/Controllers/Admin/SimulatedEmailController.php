<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SimulatedEmail;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SimulatedEmailController extends Controller
{
    public function index(Request $request): View
    {
        $query = SimulatedEmail::with('booking.room')->orderByDesc('sent_at');

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('recipient_email', 'like', $s)
                    ->orWhere('recipient_name', 'like', $s)
                    ->orWhere('subject', 'like', $s);
            });
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->filled('recipient_type')) {
            $query->where('recipient_type', $request->recipient_type);
        }

        $emails = $query->paginate(15)->withQueryString();
        $selectedEmail = null;

        if ($request->filled('view_id')) {
            $selectedEmail = SimulatedEmail::find($request->view_id);
        } elseif ($emails->isNotEmpty()) {
            $selectedEmail = $emails->first();
        }

        return view('admin.emails.index', [
            'emails' => $emails,
            'selectedEmail' => $selectedEmail,
            'filters' => $request->only(['search', 'event_type', 'recipient_type']),
        ]);
    }
}
