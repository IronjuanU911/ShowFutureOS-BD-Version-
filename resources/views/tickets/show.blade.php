<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="w-12 h-12 bg-[#511560] text-white rounded-2xl d-flex align-items-center justify-center font-mono text-base font-bold shadow-md">
                    {{ $ticket->ticket_number }}
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                            {{ $ticket->title }}
                        </h2>
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            @if($ticket->status === App\Enums\TicketStatus::Backlog) bg-slate-100 text-slate-800 border border-slate-200
                            @elseif($ticket->status === App\Enums\TicketStatus::InProgress) bg-blue-100 text-blue-800 border border-blue-200
                            @elseif($ticket->status === App\Enums\TicketStatus::Qa) bg-amber-100 text-amber-800 border border-amber-200
                            @else bg-emerald-100 text-emerald-800 border border-emerald-200 @endif">
                            {{ $ticket->status->label() }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            @if($ticket->priority === App\Enums\TicketPriority::High) bg-rose-100 text-rose-800 border border-rose-200
                            @elseif($ticket->priority === App\Enums\TicketPriority::Medium) bg-amber-100 text-amber-800 border border-amber-200
                            @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                            {{ $ticket->priority->label() }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1 d-flex align-items-center gap-3 flex-wrap">
                        <span><i class="bi bi-building"></i> Cliente: <strong class="text-gray-800"><a href="{{ route('clients.show', $ticket->client) }}" class="hover:underline">{{ $ticket->client->name }}</a></strong></span>
                        <span><i class="bi bi-person-badge"></i> Responsable: <strong class="text-gray-800">{{ $ticket->assignedUser ? $ticket->assignedUser->name : 'Sin Asignar' }}</strong></span>
                        @if($ticket->deadline)
                            <span><i class="bi bi-calendar-event"></i> Fecha Límite: <strong class="{{ $ticket->is_overdue ? 'text-rose-600 font-bold' : 'text-gray-800' }}">{{ $ticket->deadline->format('d/m/Y') }}</strong></span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('tickets.edit', $ticket) }}" class="pure-button bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 shadow-sm transition">
                    <i class="bi bi-pencil-square mr-1"></i> Editar
                </a>
                <a href="{{ route('tickets.index') }}" class="pure-button bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition">
                    <i class="bi bi-arrow-left mr-1"></i> Volver al Tablero
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-gray-50/50 min-h-screen" x-data="{ 
        activeTab: '{{ request('tab', 'overview') }}',
        aiLoading: false,
        aiData: null,
        analyzeWithAi() {
            this.aiLoading = true;
            fetch('{{ route('tickets.gemini-analyze', $ticket) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.aiData = data.insights || data;
                this.aiLoading = false;
            })
            .catch(err => {
                this.aiLoading = false;
                alert('Error analizando con Gemini IA.');
            });
        }
    }">
        <div class="container-fluid max-w-7xl space-y-4">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl shadow-sm d-flex align-items-center justify-between mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-check-circle-fill text-emerald-600 text-xl"></i>
                        <span class="text-sm font-medium text-emerald-800">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Tab Navigation Header -->
            <div class="bg-white p-2 rounded-2xl shadow-sm border border-gray-100 d-flex flex-wrap gap-2 mb-4">
                <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'bg-[#511560] text-white font-bold shadow-sm' : 'text-gray-600 hover:bg-gray-100 font-medium'" class="pure-button px-4 py-2 rounded-xl text-sm transition d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text-fill"></i> Detalle & Requerimiento
                </button>

                <button @click="activeTab = 'qa'" :class="activeTab === 'qa' ? 'bg-[#511560] text-white font-bold shadow-sm' : 'text-gray-600 hover:bg-gray-100 font-medium'" class="pure-button px-4 py-2 rounded-xl text-sm transition d-flex align-items-center gap-2">
                    <i class="bi bi-clipboard-check-fill"></i> Control QA & Aprobación
                    @if($ticket->has_approved_qa)
                        <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-emerald-200 text-emerald-900 font-bold">Aprobado</span>
                    @endif
                </button>

                <button @click="activeTab = 'comments'" :class="activeTab === 'comments' ? 'bg-[#511560] text-white font-bold shadow-sm' : 'text-gray-600 hover:bg-gray-100 font-medium'" class="pure-button px-4 py-2 rounded-xl text-sm transition d-flex align-items-center gap-2">
                    <i class="bi bi-chat-left-text-fill"></i> Comentarios ({{ $ticket->comments->count() }})
                </button>

                <button @click="activeTab = 'attachments'" :class="activeTab === 'attachments' ? 'bg-[#511560] text-white font-bold shadow-sm' : 'text-gray-600 hover:bg-gray-100 font-medium'" class="pure-button px-4 py-2 rounded-xl text-sm transition d-flex align-items-center gap-2">
                    <i class="bi bi-paperclip"></i> Adjuntos ({{ $ticket->attachments->count() }})
                </button>

                <button @click="activeTab = 'history'" :class="activeTab === 'history' ? 'bg-[#511560] text-white font-bold shadow-sm' : 'text-gray-600 hover:bg-gray-100 font-medium'" class="pure-button px-4 py-2 rounded-xl text-sm transition d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history"></i> Historial
                </button>
            </div>

            <!-- Content Area with Sidebar for Gemini AI -->
            <div class="row g-4">
                
                <!-- Main Tab Content Column -->
                <div class="col-12 col-lg-8">
                    
                    <!-- TAB 1: OVERVIEW -->
                    <div x-show="activeTab === 'overview'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                        <div class="d-flex justify-content-between align-items-center border-b border-gray-100 pb-3">
                            <h3 class="text-lg font-bold text-gray-900">Brief y Descripción</h3>
                            <span class="text-xs font-semibold text-gray-400">Creado por {{ $ticket->creator ? $ticket->creator->name : 'Sistema' }} el {{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 whitespace-pre-line text-sm text-gray-800">
                            {{ $ticket->description ?: 'Sin descripción provista.' }}
                        </div>

                        <div class="row g-4 pt-2">
                            <div class="col-12 col-sm-6">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Formato / Especificación</p>
                                <p class="text-sm font-bold text-gray-800 mt-1">{{ $ticket->format ?: 'Estándar' }}</p>
                            </div>

                            <div class="col-12 col-sm-6">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Aprobación Cliente</p>
                                <p class="text-sm font-bold mt-1 {{ $ticket->requires_client_approval ? 'text-amber-600' : 'text-gray-500' }}">
                                    {{ $ticket->requires_client_approval ? 'Requiere Visto Bueno del Cliente' : 'Aprobación Interna Agencia' }}
                                </p>
                            </div>
                        </div>

                        <!-- Status Action Controls -->
                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Cambiar Estado del Ticket</p>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(App\Enums\TicketStatus::cases() as $st)
                                    <form method="POST" action="{{ route('tickets.update-status', $ticket) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $st->value }}">
                                        <button type="submit" {{ $ticket->status->value === $st->value ? 'disabled' : '' }} class="pure-button text-xs py-1.5 px-3 rounded-lg font-semibold transition {{ $ticket->status->value === $st->value ? 'bg-[#511560] text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                            {{ $st->label() }}
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: QA REVIEW -->
                    <div x-show="activeTab === 'qa'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                        <div class="border-b border-gray-100 pb-3">
                            <h3 class="text-lg font-bold text-gray-900 d-flex align-items-center gap-2">
                                <i class="bi bi-shield-check text-[#511560]"></i> Evaluación de Control de Calidad QA
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">Checklist de verificación de estrategia, tono de marca y especificaciones técnicas antes de la entrega final.</p>
                        </div>

                        @if($ticket->qaReviews->isNotEmpty())
                            <div class="space-y-4 mb-6">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">Historial de Revisiones QA</h4>
                                @foreach($ticket->qaReviews as $review)
                                    <div class="p-4 border rounded-xl {{ $review->is_approved ? 'bg-emerald-50/50 border-emerald-200' : 'bg-rose-50/50 border-rose-200' }}">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-xs font-bold px-2.5 py-1 rounded-lg {{ $review->is_approved ? 'bg-emerald-100 text-emerald-900' : 'bg-rose-100 text-rose-900' }}">
                                                {{ $review->is_approved ? 'APROBADO' : 'RECHAZADO - REVISE OBSERVACIONES' }}
                                            </span>
                                            <span class="text-xs text-gray-500 font-medium">
                                                Revisado por {{ $review->reviewer ? $review->reviewer->name : 'AM' }} el {{ $review->created_at->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                        @if(!$review->is_approved && $review->rejection_reason)
                                            <p class="text-xs text-rose-800 font-bold bg-white p-2.5 rounded border border-rose-200 mt-2">
                                                Motivo de Devolución: {{ $review->rejection_reason }}
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- QA Evaluation Form -->
                        <form method="POST" action="{{ route('tickets.qa-reviews.store', $ticket) }}" class="pure-form pure-form-stacked space-y-4">
                            @csrf
                            
                            <div class="row g-3">
                                <!-- Block 1: Estratégico -->
                                <div class="col-12 col-md-4">
                                    <div class="p-4 border border-gray-100 rounded-xl bg-gray-50/50 space-y-2">
                                        <h5 class="text-xs font-bold uppercase text-[#511560]">1. Estratégico</h5>
                                        <label class="pure-checkbox text-xs font-medium text-gray-700 d-flex align-items-center gap-2">
                                            <input type="checkbox" name="strategic_message_aligned" value="1" checked class="rounded border-gray-300 text-[#511560]">
                                            Mensaje alineado al objetivo
                                        </label>
                                        <label class="pure-checkbox text-xs font-medium text-gray-700 d-flex align-items-center gap-2">
                                            <input type="checkbox" name="strategic_cta_clear" value="1" checked class="rounded border-gray-300 text-[#511560]">
                                            Llamado a la acción (CTA) claro
                                        </label>
                                        <label class="pure-checkbox text-xs font-medium text-gray-700 d-flex align-items-center gap-2">
                                            <input type="checkbox" name="strategic_funnel_correct" value="1" checked class="rounded border-gray-300 text-[#511560]">
                                            Etapa de embudo adecuada
                                        </label>
                                    </div>
                                </div>

                                <!-- Block 2: Marca -->
                                <div class="col-12 col-md-4">
                                    <div class="p-4 border border-gray-100 rounded-xl bg-gray-50/50 space-y-2">
                                        <h5 class="text-xs font-bold uppercase text-[#511560]">2. Tono & Marca</h5>
                                        <label class="pure-checkbox text-xs font-medium text-gray-700 d-flex align-items-center gap-2">
                                            <input type="checkbox" name="brand_tone_correct" value="1" checked class="rounded border-gray-300 text-[#511560]">
                                            Tono de voz de la marca
                                        </label>
                                        <label class="pure-checkbox text-xs font-medium text-gray-700 d-flex align-items-center gap-2">
                                            <input type="checkbox" name="brand_no_risky_claims" value="1" checked class="rounded border-gray-300 text-[#511560]">
                                            Sin promesas engañosas
                                        </label>
                                        <label class="pure-checkbox text-xs font-medium text-gray-700 d-flex align-items-center gap-2">
                                            <input type="checkbox" name="brand_policy_aligned" value="1" checked class="rounded border-gray-300 text-[#511560]">
                                            Cumple políticas de anuncios
                                        </label>
                                    </div>
                                </div>

                                <!-- Block 3: Técnico -->
                                <div class="col-12 col-md-4">
                                    <div class="p-4 border border-gray-100 rounded-xl bg-gray-50/50 space-y-2">
                                        <h5 class="text-xs font-bold uppercase text-[#511560]">3. Técnico</h5>
                                        <label class="pure-checkbox text-xs font-medium text-gray-700 d-flex align-items-center gap-2">
                                            <input type="checkbox" name="tech_format_correct" value="1" checked class="rounded border-gray-300 text-[#511560]">
                                            Formato y resolución correctos
                                        </label>
                                        <label class="pure-checkbox text-xs font-medium text-gray-700 d-flex align-items-center gap-2">
                                            <input type="checkbox" name="tech_subtitles_legible" value="1" checked class="rounded border-gray-300 text-[#511560]">
                                            Subtítulos legibles y sin cortes
                                        </label>
                                        <label class="pure-checkbox text-xs font-medium text-gray-700 d-flex align-items-center gap-2">
                                            <input type="checkbox" name="tech_duration_adequate" value="1" checked class="rounded border-gray-300 text-[#511560]">
                                            Duración óptima
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Decision Radios -->
                            <div class="p-4 border border-gray-200 rounded-xl bg-white space-y-3">
                                <label class="text-xs font-bold text-gray-800">Dictamen de Calidad QA *</label>
                                <div class="d-flex gap-4">
                                    <label class="pure-radio text-sm font-bold text-emerald-800 d-flex align-items-center gap-2 cursor-pointer">
                                        <input type="radio" name="is_approved" value="1" checked class="text-emerald-600">
                                        <i class="bi bi-check-circle-fill text-emerald-600"></i> APROBAR QA (Avanzar a Entregado)
                                    </label>
                                    <label class="pure-radio text-sm font-bold text-rose-800 d-flex align-items-center gap-2 cursor-pointer">
                                        <input type="radio" name="is_approved" value="0" class="text-rose-600">
                                        <i class="bi bi-x-circle-fill text-rose-600"></i> RECHAZAR (Devolver a En Proceso)
                                    </label>
                                </div>

                                <div>
                                    <label class="text-xs font-bold text-gray-700 mb-1">Observaciones / Motivo en caso de rechazo</label>
                                    <textarea name="rejection_reason" rows="2" placeholder="Detalla los ajustes necesarios para el especialista..." class="pure-input-1 text-sm border-gray-200 rounded-xl"></textarea>
                                </div>

                                <button type="submit" class="pure-button pure-button-primary bg-[#511560] text-white font-semibold text-xs rounded-xl py-2 px-4 shadow">
                                    Registrar Evaluación QA
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 3: COMMENTS -->
                    <div x-show="activeTab === 'comments'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                        <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">Discusión y Comentarios</h3>

                        <div class="space-y-3">
                            @forelse($ticket->comments as $comment)
                                <div class="p-3 border border-gray-100 rounded-xl bg-gray-50/50 d-flex gap-3">
                                    <div class="w-8 h-8 bg-[#f3e6f6] text-[#511560] rounded-full d-flex align-items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($comment->user ? $comment->user->name : 'U', 0, 2)) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-xs font-bold text-gray-900">{{ $comment->user ? $comment->user->name : 'Usuario' }}</span>
                                            <span class="text-[11px] text-gray-400">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <p class="text-xs text-gray-700 mt-1 whitespace-pre-line">{{ $comment->body }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 italic">No hay comentarios en este ticket.</p>
                            @endforelse
                        </div>

                        <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}" class="pure-form pure-form-stacked pt-3 border-t">
                            @csrf
                            <label class="text-xs font-bold text-gray-700 mb-1">Añadir Comentario</label>
                            <textarea name="body" rows="2" required placeholder="Escribe tu mensaje..." class="pure-input-1 text-sm border-gray-200 rounded-xl mb-2"></textarea>
                            <button type="submit" class="pure-button pure-button-primary bg-[#511560] text-white text-xs font-semibold rounded-xl">Enviar Comentario</button>
                        </form>
                    </div>

                    <!-- TAB 4: ATTACHMENTS -->
                    <div x-show="activeTab === 'attachments'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                        <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">Archivos Adjuntos</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @forelse($ticket->attachments as $att)
                                <div class="p-3 border border-gray-100 rounded-xl bg-gray-50/50 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2 truncate">
                                        <i class="bi bi-file-earmark-arrow-down text-[#511560] text-lg"></i>
                                        <div>
                                            <p class="text-xs font-bold text-gray-900 truncate max-w-[150px]">{{ $att->file_name }}</p>
                                            <p class="text-[10px] text-gray-400">{{ number_format($att->file_size / 1024, 1) }} KB</p>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('tickets.attachments.destroy', ['ticket' => $ticket->id, 'attachment' => $att->id]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('¿Eliminar adjunto?');" class="text-gray-400 hover:text-rose-600 pure-button bg-transparent border-0 p-0">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 italic col-span-full">No se han adjuntado archivos.</p>
                            @endforelse
                        </div>

                        <form method="POST" action="{{ route('tickets.attachments.store', $ticket) }}" enctype="multipart/form-data" class="pure-form pure-form-stacked pt-3 border-t space-y-2">
                            @csrf
                            <label class="text-xs font-bold text-gray-700">Subir Nuevo Adjunto</label>
                            <input type="file" name="attachment" required class="pure-input-1 text-xs border-gray-200 rounded-xl">
                            <button type="submit" class="pure-button pure-button-primary bg-[#511560] text-white text-xs font-semibold rounded-xl">Cargar Archivo</button>
                        </form>
                    </div>

                    <!-- TAB 5: HISTORY (INTEGRADO EN TICKET) -->
                    <div x-show="activeTab === 'history'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                        <div class="d-flex justify-content-between align-items-center border-b border-gray-100 pb-3">
                            <h3 class="text-lg font-bold text-gray-900">Trazabilidad e Historial</h3>
                            <span class="text-[10px] text-gray-400 font-mono">Última Actividad</span>
                        </div>

                        @if($ticket->history_new_value)
                            <div class="p-4 bg-gray-50/70 rounded-xl border border-gray-100 space-y-2">
                                <div class="d-flex justify-content-between align-items-center text-xs">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="p-1.5 bg-[#faf4fb] text-[#511560] rounded-lg">
                                            <i class="bi bi-clock-history"></i>
                                        </span>
                                        <div>
                                            <span class="text-gray-400 text-[10px] uppercase font-bold block">Responsable de la Acción</span>
                                            <strong class="text-gray-900">{{ $ticket->historyUser ? $ticket->historyUser->name : 'Sistema ShowFutureOS' }}</strong>
                                        </div>
                                    </div>
                                    <span class="text-gray-400 text-[11px] font-mono">
                                        {{ $ticket->history_updated_at ? $ticket->history_updated_at->format('d/m/Y H:i') : $ticket->updated_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>

                                <div class="pt-2 border-t border-gray-100 text-xs">
                                    <span class="text-gray-500 font-medium">Evento:</span>
                                    <span class="font-bold text-[#511560]">{{ $ticket->history_new_value }}</span>
                                    @if($ticket->history_old_value)
                                        <span class="text-gray-400 text-[11px] block mt-0.5">(Anterior: {{ $ticket->history_old_value }})</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <p class="text-xs text-gray-400 italic">Sin registros de actividad previa.</p>
                        @endif
                    </div>

                </div>

                <!-- SIDEBAR: GEMINI AI ASSISTANT WIDGET -->
                <div class="col-12 col-lg-4">
                    <div class="bg-white rounded-2xl shadow-sm border border-[#e8ceef] p-5 space-y-4">
                        <div class="d-flex align-items-center justify-between border-b border-gray-100 pb-3">
                            <h4 class="text-sm font-bold text-[#2c0b34] d-flex align-items-center gap-2">
                                <span class="p-1.5 bg-gradient-to-r from-[#511560] to-[#7a2290] text-white rounded-lg">
                                    <i class="bi bi-stars"></i>
                                </span>
                                Asistente Gemini IA
                            </h4>
                            <span class="text-[10px] bg-purple-100 text-purple-800 font-bold px-2 py-0.5 rounded-full">Inteligencia Artificial</span>
                        </div>

                        <p class="text-xs text-gray-500">Analiza el contexto del ticket, las mejores prácticas de la plataforma y el historial del cliente para recibir recomendaciones tácticas.</p>

                        <button @click="analyzeWithAi()" class="pure-button pure-button-primary bg-gradient-to-r from-[#511560] to-[#7a2290] text-white w-100 text-xs font-bold rounded-xl py-2.5 shadow d-flex align-items-center justify-center gap-2">
                            <template x-if="!aiLoading">
                                <span class="d-flex align-items-center gap-1">
                                    <i class="bi bi-magic"></i> Analizar Ticket con Gemini IA
                                </span>
                            </template>
                            <template x-if="aiLoading">
                                <span class="d-flex align-items-center gap-2">
                                    <span class="spinner-border spinner-border-sm" role="status"></span> Analizando ticket...
                                </span>
                            </template>
                        </button>

                        <!-- AI Output Panel -->
                        <div x-show="aiData" x-cloak class="mt-3 p-4 bg-[#faf4fb]/50 border border-[#e8ceef] rounded-xl space-y-3">
                            <div class="border-b border-[#e8ceef] pb-2">
                                <p class="text-xs font-bold text-[#2c0b34]">Resumen Inteligente:</p>
                                <p class="text-xs text-[#380f42] mt-1" x-text="aiData.summary || aiData.analysis"></p>
                            </div>

                            <template x-if="aiData.recommendations">
                                <div>
                                    <p class="text-xs font-bold text-[#2c0b34]">Recomendaciones de Alta Conversión:</p>
                                    <ul class="mt-1 list-disc list-inside text-xs text-[#380f42] space-y-1">
                                        <template x-for="rec in aiData.recommendations" :key="rec">
                                            <li x-text="rec"></li>
                                        </template>
                                    </ul>
                                </div>
                            </template>

                            <template x-if="aiData.qa_warnings">
                                <div>
                                    <p class="text-xs font-bold text-amber-900">Puntos Clave pre-QA:</p>
                                    <ul class="mt-1 list-disc list-inside text-xs text-amber-800 space-y-1">
                                        <template x-for="warn in aiData.qa_warnings" :key="warn">
                                            <li x-text="warn"></li>
                                        </template>
                                    </ul>
                                </div>
                            </template>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
