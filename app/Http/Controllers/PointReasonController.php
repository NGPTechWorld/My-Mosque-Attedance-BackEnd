<?php

namespace App\Http\Controllers;

use App\Models\PointReason;
use App\Models\Shift;
use Illuminate\Http\Request;

/**
 * قسم «برنامج النقاط» في لوحة الإدارة: إدارة أسباب النقاط (الاسم، النوع، الكمية، الفترات).
 */
class PointReasonController extends Controller
{
    public function index()
    {
        $reasons = PointReason::with('shifts')->orderBy('name')->get();
        return view('point_reasons.index', compact('reasons'));
    }

    public function create()
    {
        $shifts = Shift::all();
        return view('point_reasons.create', compact('shifts'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $reason = PointReason::create($data);
        $reason->shifts()->sync($request->shift_ids);

        return redirect()->route('point_reasons.index')->with('success', 'تم إضافة السبب بنجاح');
    }

    public function edit($id)
    {
        $reason = PointReason::with('shifts')->findOrFail($id);
        $shifts = Shift::all();
        return view('point_reasons.edit', compact('reason', 'shifts'));
    }

    public function update(Request $request, $id)
    {
        $reason = PointReason::findOrFail($id);
        $reason->update($this->validateData($request));
        $reason->shifts()->sync($request->shift_ids);

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
            'shift_ids' => 'required|array|min:1',
            'shift_ids.*' => 'exists:shifts,id',
        ]);

        $validated['active'] = $request->boolean('active');
        unset($validated['shift_ids']); // تُزامن عبر العلاقة لا عبر الأعمدة

        return $validated;
    }
}
