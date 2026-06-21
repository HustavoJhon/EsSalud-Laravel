@extends('layouts.app')
@section('title', 'Trámite #' . $procedure->id)
@section('page_title', 'Trámite #' . $procedure->id . ' - ' . $procedure->procedureType->name)
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Información del Trámite</h3>
                <span class="inline-block px-3 py-1 text-sm font-medium rounded-full
                    @if($procedure->status->code === 'APROBADO') bg-green-100 text-green-800
                    @elseif($procedure->status->code === 'RECHAZADO') bg-red-100 text-red-800
                    @elseif($procedure->status->code === 'SUBSANACION') bg-orange-100 text-orange-800
                    @elseif($procedure->status->code === 'CANCELADO') bg-gray-100 text-gray-800
                    @else bg-blue-100 text-blue-800 @endif">
                    {{ $procedure->status->name }}
                </span>
            </div>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500">Tipo</dt>
                    <dd class="font-medium">{{ $procedure->procedureType->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Solicitante</dt>
                    <dd class="font-medium">{{ $procedure->user->full_name ?? $procedure->user->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Fecha de Creación</dt>
                    <dd>{{ $procedure->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Radicado</dt>
                    <dd>{{ $procedure->submitted_at ? $procedure->submitted_at->format('d/m/Y H:i') : 'Pendiente' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Completado</dt>
                    <dd>{{ $procedure->completed_at ? $procedure->completed_at->format('d/m/Y H:i') : 'Pendiente' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Asignado a</dt>
                    <dd>{{ $procedure->currentAssignee?->full_name ?? 'No asignado' }}</dd>
                </div>
            </dl>
            @if($procedure->data)
                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Datos adicionales:</h4>
                    <pre class="text-xs text-gray-600 whitespace-pre-wrap">{{ json_encode($procedure->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Acciones</h3>
            <div class="flex flex-wrap gap-2">
                @if($procedure->status->code === 'BORRADOR' && $procedure->user_id === Auth::id())
                    <form method="POST" action="{{ route('procedures.submit', $procedure) }}">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm">
                            Radicar Trámite
                        </button>
                    </form>
                @endif
                @if($procedure->status->code === 'SUBSANACION' && $procedure->user_id === Auth::id())
                    <button onclick="document.getElementById('subsanar-modal').classList.remove('hidden')"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 text-sm">
                        Responder Subsanación
                    </button>
                @endif
                @can('approve', $procedure)
                    <form method="POST" action="{{ route('procedures.approve', $procedure) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm">
                            Aprobar
                        </button>
                    </form>
                @endcan
                @can('reject', $procedure)
                    <button onclick="document.getElementById('reject-modal').classList.remove('hidden')"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm">
                        Rechazar
                    </button>
                @endcan
                @can('requestSubsanacion', $procedure)
                    <button onclick="document.getElementById('subsanacion-modal').classList.remove('hidden')"
                        class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 text-sm">
                        Solicitar Subsanación
                    </button>
                @endcan
                @can('assign', $procedure)
                    <button onclick="document.getElementById('assign-modal').classList.remove('hidden')"
                        class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 text-sm">
                        Asignar
                    </button>
                @endcan
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Historial</h3>
            <div class="space-y-4">
                @foreach($procedure->histories as $history)
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 mt-2 rounded-full bg-primary-500 shrink-0"></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">{{ $history->comment }}</p>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="text-xs text-gray-500">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                                @if($history->fromStatus && $history->toStatus)
                                    <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">
                                        {{ $history->fromStatus->name }} → {{ $history->toStatus->name }}
                                    </span>
                                @endif
                                <span class="text-xs text-gray-400">{{ $history->changedBy?->name }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Comentarios</h3>
            <form method="POST" action="{{ route('procedures.comment', $procedure) }}" class="mb-6">
                @csrf
                <textarea name="comment" rows="3" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 text-sm"
                    placeholder="Escribe un comentario..."></textarea>
                <div class="flex items-center justify-between mt-2">
                    <label class="flex items-center text-sm text-gray-600">
                        <input type="checkbox" name="is_internal" value="1" class="mr-2 rounded">
                        Solo visible para operadores
                    </label>
                    <button type="submit" class="bg-primary-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-primary-700">
                        Enviar
                    </button>
                </div>
            </form>
            <div class="space-y-3">
                @foreach($procedure->comments as $comment)
                    <div class="p-3 rounded-lg {{ $comment->is_internal ? 'bg-yellow-50 border border-yellow-200' : 'bg-gray-50' }}">
                        <div class="flex items-start justify-between">
                            <p class="text-sm text-gray-800">{{ $comment->comment }}</p>
                            @if($comment->is_internal)
                                <span class="text-xs bg-yellow-200 text-yellow-800 px-2 py-0.5 rounded">Interno</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $comment->user->name }} - {{ $comment->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Documentos</h3>
            @if($procedure->documents->isNotEmpty())
                <div class="space-y-2 mb-4">
                    @foreach($procedure->documents as $doc)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <span class="text-sm font-medium">{{ $doc->original_name }}</span>
                                <span class="text-xs text-gray-500 ml-2">{{ number_format($doc->file_size / 1024, 1) }} KB</span>
                            </div>
                            @if($doc->is_validated)
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">Validado</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="flex items-end space-x-3">
                @csrf
                <input type="hidden" name="procedure_id" value="{{ $procedure->id }}">
                <div class="flex-1">
                    <input type="file" name="file" required
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                </div>
                <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-primary-700">
                    Subir
                </button>
            </form>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Subsanaciones</h3>
            @if($procedure->subsanaciones->isNotEmpty())
                @foreach($procedure->subsanaciones as $sub)
                    <div class="p-3 mb-3 bg-orange-50 rounded-lg border border-orange-200">
                        <div class="text-xs text-orange-600 font-medium">Intento #{{ $sub->attempt_number }}</div>
                        <p class="text-sm mt-1"><strong>Solicitud:</strong> {{ $sub->requested_comment }}</p>
                        @if($sub->response_comment)
                            <p class="text-sm mt-1"><strong>Respuesta:</strong> {{ $sub->response_comment }}</p>
                        @endif
                        <div class="text-xs text-gray-500 mt-1">
                            Plazo: {{ $sub->deadline->format('d/m/Y') }}
                            @if($sub->is_fulfilled)
                                <span class="text-green-600">✓ Cumplido</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-sm text-gray-500">Sin subsanaciones.</p>
            @endif
        </div>
    </div>
</div>

<div id="subsanacion-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">Solicitar Subsanación</h3>
        <form method="POST" action="{{ route('procedures.request-subsanacion', $procedure) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                <textarea name="comment" rows="4" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    placeholder="Describa las observaciones..."></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Días de plazo</label>
                <input type="number" name="deadline_days" value="5" min="1" max="30"
                    class="w-24 px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-700">Enviar</button>
                <button type="button" onclick="document.getElementById('subsanacion-modal').classList.add('hidden')"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="reject-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">Rechazar Trámite</h3>
        <form method="POST" action="{{ route('procedures.reject', $procedure) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Motivo del rechazo</label>
                <textarea name="comment" rows="4" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    placeholder="Describa el motivo del rechazo..."></textarea>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">Rechazar</button>
                <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="subsanar-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">Responder Subsanación</h3>
        <form method="POST" action="{{ route('procedures.subsanar', $procedure) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Respuesta</label>
                <textarea name="response_comment" rows="4" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    placeholder="Describa su respuesta..."></textarea>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-700">Enviar</button>
                <button type="button" onclick="document.getElementById('subsanar-modal').classList.add('hidden')"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="assign-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">Asignar Trámite</h3>
        <form method="POST" action="{{ route('procedures.assign', $procedure) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Operador</label>
                <select name="assignee_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Seleccionar operador...</option>
                    @foreach($operators as $op)
                        <option value="{{ $op->id }}" {{ $procedure->current_assignee_id == $op->id ? 'selected' : '' }}>
                            {{ $op->full_name ?? $op->name }} ({{ $op->role }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700">Asignar</button>
                <button type="button" onclick="document.getElementById('assign-modal').classList.add('hidden')"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm">Cancelar</button>
            </div>
        </form>
    </div>
</div>
@endsection
