<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        $stats = [
            'users' => User::count(),
            'roles' => Role::count(),
            'audits_today' => null,
            'logins_today' => null,
        ];

        $recentAudits = collect();

        if ($user->hasPermission('audit.view')) {
            $stats['audits_today'] = AuditLog::whereDate('created_at', today())->count();
            $stats['logins_today'] = AuditLog::where('event', 'login')->whereDate('created_at', today())->count();

            $recentAudits = AuditLog::query()
                ->latest('id')
                ->limit(10)
                ->get();
        }

        return view('dashboard', compact('stats', 'recentAudits'));
    }
}
