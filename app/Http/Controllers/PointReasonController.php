<?php

namespace App\Http\Controllers;

use App\Models\PointReason;
use Illuminate\Http\Request;

/**
 * قسم «برنامج النقاط» في لوحة الإدارة: إدارة أسباب النقاط (الاسم، النوع، الكمية).
 */
class PointReasonController extends Controller
{
    public function index()
    {
        $reasons = PointReason::orderBy('name')->get();
        return view('point_reasons.index', compact('reasons'));
    }

    public function create()
    {
        return view('point_reasons.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        PointReason::create($data);

        return redirect()->route('point_reasons.index')->with('success', 'تم إضافة السبب بنجاح');
    }

    public function edit($id)
    {
        $reason = PointReason::findOrFail($id);
        return view('point_reasons.edit', compact('reason'));
    }

    public function update(Request $request, $id)
    {
        $reason = PointReason::findOrFail($id);
        $reason->update($this->validateData($request));

        return redirect()->route('point_reasons.index')->with('success', 'تم تعديل السبب');
    }

    public function destroy($id)
    {
        PointReason::findOrFail($id)->delete();
        return redirect()->route('point_reasons.index')->with('success', 'تم حذف السبب');
    }

    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:add,remove',
            'amount' => 'required|integer|min:1',
            'active' => 'nullable|boolean',
        ]);

        $validated['active'] = $request->boolean('active');

        return $validated;
    }
}
