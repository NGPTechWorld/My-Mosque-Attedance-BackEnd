<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::all();
        return view('shifts.index', compact('shifts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'days' => 'required|array',
        ]);
        Shift::create($data);
        return back()->with('success', 'تمت إضافة الفترة');
    }

    public function destroy($id)
    {
        Shift::destroy($id);
        return back()->with('success', 'تم حذف الفترة');
    }
}
