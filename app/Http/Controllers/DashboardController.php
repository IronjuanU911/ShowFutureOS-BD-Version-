<?php

namespace App\Http\Controllers;

use App\Enums\ClientLevel;
use App\Enums\ClientStatus;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the master client 360 & executive operations dashboard.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $level = $request->input('level');
        $amId = $request->input('account_manager_id');
        $platform = $request->input('platform');

        // Query clients with eager loading and counts
        $clientQuery = Client::with([
            'accountManager',
            'onboarding',
        ])->withCount(['contacts', 'tickets']);

        if ($search) {
            $clientQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('main_platform', 'like', "%{$search}%")
                  ->orWhere('main_objective', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $clientQuery->where('status', $status);
        }

        if ($level) {
            $clientQuery->where('level', $level);
        }

        if ($amId) {
            $clientQuery->where('account_manager_id', $amId);
        }

        if ($platform) {
            $clientQuery->where('main_platform', $platform);
        }

        $clients = $clientQuery->orderByDesc('created_at')->paginate(15)->withQueryString();

        // Optimized single-query aggregations via GROUP BY
        $clientStatusCounts = Client::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');
        $activeClientsCount = $clientStatusCounts[ClientStatus::Active->value] ?? 0;
        $onboardingClientsCount = $clientStatusCounts[ClientStatus::Onboarding->value] ?? 0;
        $pausedClientsCount = $clientStatusCounts[ClientStatus::Paused->value] ?? 0;
        $totalClientsCount = $clientStatusCounts->sum();
        $totalAdsBudget = Client::sum('ads_budget') ?? 0;
        $slaGuaranteedCount = Client::where('has_sla', true)->count();

        // Onboarding KPIs
        $avgOnboardingProgress = round(\App\Models\Onboarding::avg('progress_percentage') ?? 0);
        $completedOnboardingsCount = \App\Models\Onboarding::where('status', 'completed')->count();

        // Tickets & Operations KPIs (single query)
        $ticketStatusCounts = Ticket::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');
        $backlogTicketsCount = $ticketStatusCounts[TicketStatus::Backlog->value] ?? 0;
        $inProgressTicketsCount = $ticketStatusCounts[TicketStatus::InProgress->value] ?? 0;
        $qaTicketsCount = $ticketStatusCounts[TicketStatus::Qa->value] ?? 0;
        $deliveredTicketsCount = $ticketStatusCounts[TicketStatus::Delivered->value] ?? 0;
        $totalTicketsCount = $ticketStatusCounts->sum();

        $highPriorityTicketsCount = Ticket::where('priority', TicketPriority::High)
            ->whereIn('status', [TicketStatus::Backlog, TicketStatus::InProgress, TicketStatus::Qa])
            ->count();

        // Level distribution (single query)
        $levelCountsRaw = Client::selectRaw('level, count(*) as count')->groupBy('level')->pluck('count', 'level');
        $levelCounts = [
            'starter' => $levelCountsRaw[ClientLevel::Starter->value] ?? 0,
            'growth' => $levelCountsRaw[ClientLevel::Growth->value] ?? 0,
            'scale' => $levelCountsRaw[ClientLevel::Scale->value] ?? 0,
        ];

        // Top platforms
        $platforms = Client::whereNotNull('main_platform')
            ->select('main_platform')
            ->distinct()
            ->pluck('main_platform');

        // Account Managers list
        $accountManagers = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['admin', 'account-manager']);
        })->get();

        // Critical / Urgent items
        $urgentTickets = Ticket::with(['client', 'assignedUser'])
            ->whereIn('status', [TicketStatus::Backlog, TicketStatus::InProgress, TicketStatus::Qa])
            ->where(function ($q) {
                $q->where('priority', TicketPriority::High)
                  ->orWhere('deadline', '<=', now()->addDays(2));
            })
            ->orderBy('deadline')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'clients',
            'totalClientsCount',
            'activeClientsCount',
            'onboardingClientsCount',
            'pausedClientsCount',
            'totalAdsBudget',
            'slaGuaranteedCount',
            'avgOnboardingProgress',
            'completedOnboardingsCount',
            'totalTicketsCount',
            'backlogTicketsCount',
            'inProgressTicketsCount',
            'qaTicketsCount',
            'deliveredTicketsCount',
            'highPriorityTicketsCount',
            'levelCounts',
            'platforms',
            'accountManagers',
            'urgentTickets'
        ));
    }
}
