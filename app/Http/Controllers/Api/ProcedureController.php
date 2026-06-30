<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Procedure;
use App\Models\ProcedureHistory;
use App\Models\ProcedureStatus;
use App\Models\ProcedureType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProcedureController extends Controller
{
    public function index(Request $request)
    {
        $query = Procedure::with(['user', 'procedureType', 'status']);

        if ($request->filled('status')) {
            $query->whereHas('status', fn($q) => $q->where('code', $request->status));
        }

        $procedures = $query->latest()->paginate($request->per_page ?? 20);
        return response()->json($procedures);
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
            'user_id' => $request->user()->id,
            'procedure_type_id' => $validated['procedure_type_id'],
            'procedure_status_id' => $borrador->id,
            'data' => $validated['data'] ?? null,
            'idempotency_key' => $validated['idempotency_key'] ?? Str::uuid(),
        ]);

        ProcedureHistory::create([
            'procedure_id' => $procedure->id,
            'from_status_id' => null,
            'to_status_id' => $borrador->id,
            'changed_by' => $request->user()->id,
            'comment' => 'Trámite creado vía API.',
        ]);

        return response()->json($procedure->load(['user', 'procedureType', 'status']), 201);
    }

    public function show(Procedure $procedure)
    {
        $procedure->load([
            'user', 'procedureType', 'status', 'currentAssignee',
            'histories.fromStatus', 'histories.toStatus', 'histories.changedBy',
            'comments.user', 'subsanaciones.requestedBy', 'documents.category',
        ]);

        return response()->json($procedure);
    }

    public function my(Request $request)
    {
        $procedures = Procedure::where('user_id', $request->user()->id)
            ->with(['procedureType', 'status'])
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json($procedures);
    }

    public function types()
    {
        $types = ProcedureType::where('is_active', true)->get();
        return response()->json($types);
    }

    public function statuses()
    {
        $statuses = ProcedureStatus::all();
        return response()->json($statuses);
    }

    public function history(Procedure $procedure)
    {
        $history = $procedure->histories()
            ->with(['fromStatus', 'toStatus', 'changedBy'])
            ->latest()
            ->get();

        return response()->json($history);
    }

    public function documents(Procedure $procedure)
    {
        $documents = $procedure->documents()->with('category')->latest()->get();
        return response()->json($documents);
    }
}
