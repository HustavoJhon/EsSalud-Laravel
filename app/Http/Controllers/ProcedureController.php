<?php

namespace App\Http\Controllers;

use App\Models\Procedure;
use App\Models\ProcedureHistory;
use App\Models\ProcedureStatus;
use App\Models\ProcedureType;
use App\Models\Subsanacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ProcedureController extends Controller
{
    public function index(Request $request)
    {
        $query = Procedure::with(['user', 'procedureType', 'status']);

        if ($request->filled('status')) {
            $query->whereHas('status', fn($q) => $q->where('code', $request->status));
        }

        if ($request->filled('type')) {
            $query->whereHas('procedureType', fn($q) => $q->where('code', $request->type));
        }

        if (Auth::user()->hasRole('ASEG')) {
            $query->where('user_id', Auth::id());
        }

        $procedures = $query->latest()->paginate(20);
        $statuses = ProcedureStatus::all();
        $types = ProcedureType::where('is_active', true)->get();

        return view('procedures.index', compact('procedures', 'statuses', 'types'));
    }

    public function show(Procedure $procedure)
    {
        if (Gate::denies('view', $procedure)) {
            abort(403);
        }

        $procedure->load(['user', 'procedureType', 'status', 'currentAssignee',
            'histories' => fn($q) => $q->with(['fromStatus', 'toStatus', 'changedBy'])->latest(),
            'comments' => fn($q) => $q->with('user')->latest(),
            'subsanaciones' => fn($q) => $q->with('requestedBy')->latest(),
            'documents' => fn($q) => $q->with('category')->latest(),
        ]);

        $operators = User::role(['OPER', 'SUPV', 'GESDOC'])->get();

        return view('procedures.show', compact('procedure', 'operators'));
    }

    public function create()
    {
        $types = ProcedureType::where('is_active', true)->get();
        return view('procedures.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'procedure_type_id' => 'required|exists:procedure_types,id',
            'data' => 'nullable|array',
            'idempotency_key' => 'nullable|string|max:64|unique:procedures,idempotency_key',
        ]);

        $borrador = ProcedureStatus::where('code', 'BORRADOR')->first();

        $procedure = Procedure::create([
            'user_id' => Auth::id(),
            'procedure_type_id' => $validated['procedure_type_id'],
            'procedure_status_id' => $borrador->id,
            'data' => $validated['data'] ?? null,
            'idempotency_key' => $validated['idempotency_key'] ?? Str::uuid(),
        ]);

        ProcedureHistory::create([
            'procedure_id' => $procedure->id,
            'from_status_id' => null,
            'to_status_id' => $borrador->id,
            'changed_by' => Auth::id(),
            'comment' => 'Trámite creado.',
        ]);

        return redirect()->route('procedures.show', $procedure)
            ->with('status', 'Trámite creado exitosamente.');
    }

    public function update(Request $request, Procedure $procedure)
    {
        if (Gate::denies('update', $procedure)) {
            abort(403);
        }

        $validated = $request->validate([
            'data' => 'nullable|array',
        ]);

        $procedure->update(['data' => $validated['data'] ?? $procedure->data]);

        return back()->with('status', 'Trámite actualizado.');
    }

    public function submit(Procedure $procedure)
    {
        if (Gate::denies('submit', $procedure)) {
            abort(403);
        }

        $radicado = ProcedureStatus::where('code', 'RADICADO')->first();
        $oldStatus = $procedure->status;

        $procedure->update([
            'procedure_status_id' => $radicado->id,
            'submitted_at' => now(),
        ]);

        ProcedureHistory::create([
            'procedure_id' => $procedure->id,
            'from_status_id' => $oldStatus->id,
            'to_status_id' => $radicado->id,
            'changed_by' => Auth::id(),
            'comment' => 'Trámite radicado.',
        ]);

        return back()->with('status', 'Trámite radicado exitosamente.');
    }

    public function approve(Procedure $procedure, Request $request)
    {
        if (Gate::denies('approve', $procedure)) {
            abort(403);
        }

        $aprobado = ProcedureStatus::where('code', 'APROBADO')->first();
        $oldStatus = $procedure->status;

        $procedure->update([
            'procedure_status_id' => $aprobado->id,
            'completed_at' => now(),
        ]);

        ProcedureHistory::create([
            'procedure_id' => $procedure->id,
            'from_status_id' => $oldStatus->id,
            'to_status_id' => $aprobado->id,
            'changed_by' => Auth::id(),
            'comment' => $request->comment ?? 'Trámite aprobado.',
        ]);

        return back()->with('status', 'Trámite aprobado exitosamente.');
    }

    public function reject(Procedure $procedure, Request $request)
    {
        if (Gate::denies('reject', $procedure)) {
            abort(403);
        }

        $rechazado = ProcedureStatus::where('code', 'RECHAZADO')->first();
        $oldStatus = $procedure->status;

        $procedure->update([
            'procedure_status_id' => $rechazado->id,
            'completed_at' => now(),
        ]);

        ProcedureHistory::create([
            'procedure_id' => $procedure->id,
            'from_status_id' => $oldStatus->id,
            'to_status_id' => $rechazado->id,
            'changed_by' => Auth::id(),
            'comment' => $request->comment ?? 'Trámite rechazado.',
        ]);

        return back()->with('status', 'Trámite rechazado.');
    }

    public function requestSubsanacion(Procedure $procedure, Request $request)
    {
        if (Gate::denies('requestSubsanacion', $procedure)) {
            abort(403);
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
            'deadline_days' => 'nullable|integer|min:1|max:30',
        ]);

        $subsanacionStatus = ProcedureStatus::where('code', 'SUBSANACION')->first();
        $oldStatus = $procedure->status;
        $deadlineDays = $validated['deadline_days'] ?? 5;

        $lastAttempt = $procedure->subsanaciones()->max('attempt_number') ?? 0;

        $procedure->update([
            'procedure_status_id' => $subsanacionStatus->id,
        ]);

        ProcedureHistory::create([
            'procedure_id' => $procedure->id,
            'from_status_id' => $oldStatus->id,
            'to_status_id' => $subsanacionStatus->id,
            'changed_by' => Auth::id(),
            'comment' => 'Trámite requiere subsanación: ' . $validated['comment'],
        ]);

        Subsanacion::create([
            'procedure_id' => $procedure->id,
            'attempt_number' => $lastAttempt + 1,
            'requested_by' => Auth::id(),
            'requested_comment' => $validated['comment'],
            'deadline' => now()->addDays($deadlineDays),
        ]);

        return back()->with('status', 'Solicitud de subsanación enviada.');
    }

    public function subsanar(Procedure $procedure, Request $request)
    {
        if (Gate::denies('subsanar', $procedure)) {
            abort(403);
        }

        $validated = $request->validate([
            'response_comment' => 'required|string|max:2000',
        ]);

        $evaluacionStatus = ProcedureStatus::where('code', 'EVALUACION')->first();
        $oldStatus = $procedure->status;

        $procedure->update([
            'procedure_status_id' => $evaluacionStatus->id,
        ]);

        $currentSubsanacion = $procedure->subsanaciones()
            ->where('is_fulfilled', false)
            ->latest()
            ->first();

        if ($currentSubsanacion) {
            $currentSubsanacion->update([
                'responded_at' => now(),
                'response_comment' => $validated['response_comment'],
                'is_fulfilled' => true,
            ]);
        }

        ProcedureHistory::create([
            'procedure_id' => $procedure->id,
            'from_status_id' => $oldStatus->id,
            'to_status_id' => $evaluacionStatus->id,
            'changed_by' => Auth::id(),
            'comment' => 'Subsanación respondida.',
        ]);

        return back()->with('status', 'Subsanación enviada correctamente.');
    }

    public function assign(Procedure $procedure, Request $request)
    {
        if (Gate::denies('assign', $procedure)) {
            abort(403);
        }

        $validated = $request->validate([
            'assignee_id' => 'required|exists:users,id',
        ]);

        $procedure->update(['current_assignee_id' => $validated['assignee_id']]);

        ProcedureHistory::create([
            'procedure_id' => $procedure->id,
            'from_status_id' => $procedure->procedure_status_id,
            'to_status_id' => $procedure->procedure_status_id,
            'changed_by' => Auth::id(),
            'comment' => 'Trámite asignado a ' . User::find($validated['assignee_id'])->full_name,
        ]);

        return back()->with('status', 'Trámite asignado correctamente.');
    }

    public function storeComment(Procedure $procedure, Request $request)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
            'is_internal' => 'boolean',
        ]);

        $procedure->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $validated['comment'],
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        return back()->with('status', 'Comentario agregado.');
    }
}
