<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();
        return response()->json([
            'status' => 'success',
            'students' => $students,
        ]);
    }

    public function show($id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'الطالب غير موجود'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'student' => $student,
        ]);
    }
public function destroy($id)
{
    $student = Student::find($id);

    if (!$student) {
        return response()->json([
            'status' => 'error',
            'message' => 'الطالب غير موجود',
        ], 404);
    }

    try {
        $student->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف الطالب بنجاح',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'حدث خطأ أثناء الحذف',
        ], 500);
    }
}

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'خطأ في بيانات الإدخال',
                'errors' => $e->errors(), 
            ], 422);
        }

        $student = Student::create([
            'name' => $validatedData['name'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تمت إضافة الطالب بنجاح',
            'student' => $student,
        ]);
    }
}
