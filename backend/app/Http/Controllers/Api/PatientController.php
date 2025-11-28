<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;

class PatientController extends Controller {
    public function index() {
        return Patient::withCount('visits')->paginate(20);
    }

   public function store(Request $request)
{
    // Validasi data 
    $data = $request->validate([
        'name' => 'required|string',
        'phone' => 'nullable|string',
        'email' => 'nullable|email',
        'birth_date' => 'nullable|date',
        'gender' => 'nullable|string',
        'address' => 'nullable|string'
    ]);

    // Ambil MR terakhir
    $last = Patient::orderBy('id', 'desc')->first();
    
    if (!$last) {
        $nextNumber = 'MR-001'; // Data pertama
    } else {
        // Ambil angka terakhir
        $lastNumber = intval(substr($last->medical_record_number, 3));
        $nextNumber = 'MR-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    // Tambahkan MR otomatis
    $data['medical_record_number'] = $nextNumber;

    // Simpan data
    $patient = Patient::create($data);

    return response()->json($patient, 201);
}


    public function show(Patient $patient) {
        return $patient->load('visits.user');
    }
}
