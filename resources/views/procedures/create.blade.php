@extends('layouts.app')
@section('title', 'Nuevo Trámite')
@section('page_title', 'Nuevo Trámite')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('procedures.store') }}">
            @csrf
            <div class="mb-6">
                <label for="procedure_type_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Trámite</label>
                <select name="procedure_type_id" id="procedure_type_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">Seleccione un tipo de trámite...</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ old('procedure_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }} - {{ $type->code }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="type-details" class="mb-6 hidden">
                <div id="type-description" class="text-sm text-gray-600 mb-3 p-3 bg-blue-50 rounded-lg"></div>
                <div id="type-requirements" class="text-sm mb-3">
                    <h4 class="font-medium text-gray-700 mb-1">Requisitos:</h4>
                    <ul id="req-list" class="list-disc text-gray-600 ml-5 space-y-1"></ul>
                </div>
                <div id="type-resolution" class="text-sm text-gray-500 mb-3"></div>
            </div>

            <div class="mb-6">
                <label for="idempotency_key" class="block text-sm font-medium text-gray-700 mb-2">Clave de Idempotencia (opcional)</label>
                <input type="text" name="idempotency_key" id="idempotency_key" value="{{ old('idempotency_key') }}" maxlength="64"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                    placeholder="Dejar en blanco para generar automáticamente">
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700">
                    Crear Trámite
                </button>
                <a href="{{ route('procedures.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const types = @json($types);
document.getElementById('procedure_type_id').addEventListener('change', function() {
    const details = document.getElementById('type-details');
    const selected = types.find(t => t.id == this.value);
    if (selected) {
        document.getElementById('type-description').textContent = selected.description || '';
        document.getElementById('type-resolution').textContent = 'Días máx. de resolución: ' + selected.max_days_resolution;
        const reqList = document.getElementById('req-list');
        reqList.innerHTML = '';
        if (selected.requirements) {
            const reqs = typeof selected.requirements === 'string'
                ? JSON.parse(selected.requirements)
                : selected.requirements;
            reqs.forEach(r => {
                const li = document.createElement('li');
                li.textContent = r;
                reqList.appendChild(li);
            });
        }
        details.classList.remove('hidden');
    } else {
        details.classList.add('hidden');
    }
});
</script>
@endpush
@endsection
