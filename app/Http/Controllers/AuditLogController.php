<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'event' => (string) $request->query('event'),
            'type' => (string) $request->query('type'),
            'q' => trim((string) $request->query('q')),
            'from' => (string) $request->query('from'),
            'to' => (string) $request->query('to'),
        ];

        $logs = AuditLog::query()
            ->with('user')
            ->when($filters['event'] !== '', fn ($query) => $query->where('event', $filters['event']))
            ->when($filters['type'] !== '', fn ($query) => $query->where('auditable_type', $filters['type']))
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $term = "%{$filters['q']}%";

                $query->where(function ($query) use ($term) {
                    $query->where('user_name', 'ilike', $term)
                        ->orWhere('user_email', 'ilike', $term)
                        ->orWhere('auditable_label', 'ilike', $term)
                        ->orWhere('auditable_type', 'ilike', $term)
                        ->orWhere('url', 'ilike', $term)
                        ->orWhere('ip_address', 'ilike', $term);
                });
            })
            ->when($filters['from'] !== '', fn ($query) => $query->whereDate('created_at', '>=', $filters['from']))
            ->when($filters['to'] !== '', fn ($query) => $query->whereDate('created_at', '<=', $filters['to']))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $events = AuditLog::query()
            ->select('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        $types = AuditLog::query()
            ->whereNotNull('auditable_type')
            ->select('auditable_type')
            ->distinct()
            ->orderBy('auditable_type')
            ->pluck('auditable_type');

        return view('audit.index', compact('logs', 'events', 'types', 'filters'));
    }

    public function show(AuditLog $auditLog): View
    {
        $auditLog->load('user');

        return view('audit.show', compact('auditLog'));
    }
}
