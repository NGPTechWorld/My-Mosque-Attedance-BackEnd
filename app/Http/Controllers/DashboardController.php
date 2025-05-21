<?php

namespace App\Http\Controllers;

use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
{
    $students = Student::all();
    return view('dashboard', compact('students'));
}

public function showAttendance($id)
{
    $student = Student::with('attendances')->findOrFail($id);
    return response()->json($student->attendances);
}

}
