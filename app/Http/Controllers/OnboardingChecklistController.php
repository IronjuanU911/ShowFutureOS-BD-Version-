<?php

namespace App\Http\Controllers;

use App\Enums\ClientStatus;
use App\Models\Client;
use App\Models\OnboardingChecklistItem;
use Illuminate\Http\Request;

class OnboardingChecklistController extends Controller
{
    /**
     * Toggle status of an onboarding checklist item.
     */
    public function toggle(Request $request, Client $client, OnboardingChecklistItem $item)
    {
        $item->update([
            'is_completed' => !$item->is_completed,
            'completed_at' => !$item->is_completed ? now() : null,
            'completed_by' => !$item->is_completed ? auth()->id() : null,
        ]);

        // Recalculate progress for the parent onboarding
        $onboarding = $client->onboarding;
        if ($onboarding) {
            $totalItems = $onboarding->items()->count();
            $completedItems = $onboarding->items()->where('is_completed', true)->count();

            $percentage = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;

            $onboardingStatus = 'in_progress';
            if ($percentage === 100) {
                $onboardingStatus = 'completed';
                $onboarding->completed_at = now();

                // Auto-transition Client status to Active if currently in Onboarding
                if ($client->status === ClientStatus::Onboarding) {
                    $client->update(['status' => ClientStatus::Active]);
                }
            } else {
                $onboarding->completed_at = null;
            }

            $onboarding->update([
                'progress_percentage' => $percentage,
                'status' => $onboardingStatus,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_completed' => $item->is_completed,
                'progress_percentage' => $onboarding ? $onboarding->progress_percentage : 0,
                'client_status' => $client->fresh()->status->label(),
            ]);
        }

        return redirect()->route('clients.show', ['client' => $client->id, 'tab' => 'onboarding'])
            ->with('success', 'Item de onboarding actualizado.');
    }

    /**
     * Add a custom checklist item.
     */
    public function storeItem(Request $request, Client $client)
    {
        $validated = $request->validate([
            'day_number' => 'required|integer|min:1|max:30',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $onboarding = $client->onboarding;
        if (!$onboarding) {
            $onboarding = $client->onboarding()->create([
                'started_at' => now(),
                'target_completion_date' => now()->addDays(7),
                'progress_percentage' => 0,
                'status' => 'in_progress',
            ]);
        }

        $maxOrder = $onboarding->items()->max('order') ?? 0;

        $onboarding->items()->create([
            'day_number' => $validated['day_number'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_completed' => false,
            'order' => $maxOrder + 1,
        ]);

        // Recalculate progress
        $totalItems = $onboarding->items()->count();
        $completedItems = $onboarding->items()->where('is_completed', true)->count();
        $percentage = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;
        $onboarding->update(['progress_percentage' => $percentage]);

        return redirect()->route('clients.show', ['client' => $client->id, 'tab' => 'onboarding'])
            ->with('success', 'Tarea personalizada agregada al checklist de onboarding.');
    }
}
