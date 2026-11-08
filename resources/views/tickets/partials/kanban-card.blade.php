<div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition space-y-3">
    <!-- Header Badge Line -->
    <div class="d-flex align-items-center justify-between gap-1 flex-wrap">
        <span class="font-mono text-[11px] font-bold bg-[#faf4fb] text-[#511560] px-2 py-0.5 rounded border border-[#e8ceef]">
            {{ $ticket->ticket_number }}
        </span>
        <div class="d-flex align-items-center gap-1">
            <!-- Priority Badge -->
            <span class="px-2 py-0.5 text-[10px] font-bold rounded
                @if($ticket->priority === App\Enums\TicketPriority::High) bg-rose-100 text-rose-700 border border-rose-200
                @elseif($ticket->priority === App\Enums\TicketPriority::Medium) bg-amber-100 text-amber-800 border border-amber-200
                @else bg-gray-100 text-gray-700 border border-gray-200 @endif">
                {{ $ticket->priority->label() }}
            </span>
            <!-- Type Badge -->
            <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-purple-50 text-purple-700 border border-purple-100">
                {{ $ticket->type ? $ticket->type->label() : 'General' }}
            </span>
        </div>
    </div>

    <!-- Title & Link -->
    <h4 class="text-sm font-bold text-gray-900 leading-snug hover:text-[#511560] transition">
        <a href="{{ route('tickets.show', $ticket) }}">{{ $ticket->title }}</a>
    </h4>

    <!-- Client Name -->
    <p class="text-xs text-gray-500 font-medium d-flex align-items-center gap-1">
        <i class="bi bi-building text-gray-400"></i>
        {{ $ticket->client ? $ticket->client->name : 'Cliente General' }}
    </p>

    <!-- Meta Info: Assignee & Deadline -->
    <div class="d-flex align-items-center justify-between pt-2 border-t border-gray-100 text-xs">
        <div class="d-flex align-items-center gap-1.5" title="Asignado a">
            <div class="w-6 h-6 bg-[#f3e6f6] text-[#511560] rounded-full d-flex align-items-center justify-center font-bold text-[10px]">
                {{ $ticket->assignedUser ? strtoupper(substr($ticket->assignedUser->name, 0, 2)) : '?' }}
            </div>
            <span class="text-xs text-gray-600 font-medium truncate max-w-[90px]">
                {{ $ticket->assignedUser ? strtok($ticket->assignedUser->name, ' ') : 'Sin asignar' }}
            </span>
        </div>

        @if($ticket->deadline)
            <div class="text-[11px] font-semibold {{ $ticket->is_overdue ? 'text-rose-600 font-bold' : 'text-gray-500' }}" title="Fecha Límite">
                <i class="bi bi-calendar-event"></i> {{ $ticket->deadline->format('d/m') }}
            </div>
        @endif
    </div>

    <!-- Quick Status Transition Action -->
    <div class="pt-2">
        @if(isset($nextStatus) && isset($nextLabel))
            <form method="POST" action="{{ route('tickets.update-status', $ticket) }}">
                @csrf
                <input type="hidden" name="status" value="{{ $nextStatus }}">
                <button type="submit" class="pure-button bg-gray-50 hover:bg-[#faf4fb] hover:text-[#511560] text-gray-600 w-100 text-xs py-1 px-2 rounded-lg font-semibold border border-gray-200 transition d-flex align-items-center justify-center gap-1">
                    <span>{{ $nextLabel }}</span>
                    <i class="bi bi-arrow-right-short text-base"></i>
                </button>
            </form>
        @elseif(isset($showQaAction) && $showQaAction)
            <a href="{{ route('tickets.show', ['ticket' => $ticket->id, 'tab' => 'qa']) }}" class="pure-button bg-amber-50 hover:bg-amber-100 text-amber-900 w-100 text-xs py-1 px-2 rounded-lg font-bold border border-amber-200 transition d-flex align-items-center justify-center gap-1">
                <i class="bi bi-clipboard-check"></i>
                <span>Evaluar en QA</span>
            </a>
        @elseif(isset($isDelivered) && $isDelivered)
            <div class="text-[11px] text-emerald-700 font-bold bg-emerald-50 py-1 px-2 rounded-lg border border-emerald-200 text-center d-flex align-items-center justify-center gap-1">
                <i class="bi bi-check-circle-fill"></i>
                <span>Entregado</span>
            </div>
        @endif
    </div>
</div>
