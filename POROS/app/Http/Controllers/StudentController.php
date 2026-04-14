<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student; // Jangan lupa import modelnya

class StudentController extends Controller
{
    // Menampilkan halaman form tambah siswa
    public function create()
    {
        return view('students.create');
    }
    // Menampilkan daftar semua siswa
    public function index()
    {
        // Mengambil semua data siswa dari database, diurutkan dari yang terbaru
        $students = Student::latest()->get(); 
        
        return view('students.index', compact('students'));
    }
    // Menyimpan data ke database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => 'required|unique:students,nisn',
            'name' => 'required',
            'gender' => 'required',
            'class' => 'required',
            'allergies' => 'nullable',
            'basic_health_notes' => 'nullable',
        ]);

        Student::create($validated);

        return redirect()->back()->with('success', 'Data siswa dan alergi berhasil disimpan!');
    }
}
