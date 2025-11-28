<?php 

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visit;
use App\Models\Patient;

class VisitController extends Controller {
    public function index() {
        return Visit::with(['patient','user'])->latest()->paginate(20);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'patient_id'=>'required|exists:patients,id',
            'visit_at'=>'required|date',
            'visit_type'=>'nullable|string',
            'notes'=>'nullable|string'
        ]);
        $data['user_id'] = $request->user()->id;
        $visit = Visit::create($data);
        return response()->json($visit->load('patient','user'),201);
    }

    public function show(Visit $visit) {
        return $visit->load('patient','user');
    }
}
