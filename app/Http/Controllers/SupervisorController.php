<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shift;
use App\Support\AdminPanel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * إدارة حسابات المشرفين: إنشاء/تعديل حساب، منح صلاحيات أقسام، وإسناد فترات.
 * متاح للمدير العام فقط (مضبوط عبر middleware القسم).
 */
class SupervisorController extends Controller
{
    public function index()
    {
        $supervisors = User::where('role', 'supervisor')->latest()->get();
        $sections = AdminPanel::SECTIONS;
        $shifts = Shift::all()->keyBy('id');

        return view('supervisors.index', compact('supervisors', 'sections', 'shifts'));
    }

    public function create()
    {
        $sections = AdminPanel::SECTIONS;
        $shifts = Shift::all();

        return view('supervisors.create', compact('sections', 'shifts'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            // عمود email إلزامي وفريد في القاعدة، والدخول يتم باسم المستخدم،
            // فنولّد بريداً مشتقاً من اسم المستخدم (فريد لأن اسم المستخدم فريد).
            'email' => $this->emailFor($data['username']),
            'password' => Hash::make($data['password']),
            'role' => 'supervisor',
            'permissions' => $data['permissions'],
            'shift_ids' => $data['shift_ids'],
        ]);

        return redirect()->route('supervisors.index')->with('success', 'تم إضافة المشرف بنجاح');
    }

    public function edit(User $user)
    {
        $sections = AdminPanel::SECTIONS;
        $shifts = Shift::all();

        return view('supervisors.edit', compact('user', 'sections', 'shifts'));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validateData($request, $user->id);

        $user->name = $data['name'];
        $user->username = $data['username'];
        $user->email = $this->emailFor($data['username']);
        $user->role = 'supervisor';
        $user->permissions = $data['permissions'];
        $user->shift_ids = $data['shift_ids'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('supervisors.index')->with('success', 'تم تحديث بيانات المشرف بنجاح');
    }

    public function destroy(User $user)
    {
        // لا نسمح بحذف مدير عام من هنا
        if ($user->isAdmin()) {
            return back()->with('error', 'لا يمكن حذف حساب مدير عام.');
        }

        $user->delete();

        return back()->with('success', 'تم حذف المشرف بنجاح');
    }

    /**
     * توليد بريد مشتق من اسم المستخدم (عمود email إلزامي وفريد في القاعدة).
     * إضافة لاحقة ثابتة لاسم مستخدم فريد تُبقي البريد فريداً.
     */
    private function emailFor(string $username): string
    {
        return trim($username) . '@mosque.local';
    }

    /** التحقق من المدخلات (password مطلوبة عند الإنشاء فقط). */
    private function validateData(Request $request, ?int $userId = null): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($userId)],
            'password' => $userId ? 'nullable|string|min:4' : 'required|string|min:4',
            'permissions' => 'nullable|array',
            'permissions.*' => ['string', Rule::in(array_keys(AdminPanel::SECTIONS))],
            'shift_ids' => 'nullable|array',
            'shift_ids.*' => 'integer|exists:shifts,id',
        ]);

        // قيم افتراضية لمصفوفات فارغة
        $validated['permissions'] = array_values($validated['permissions'] ?? []);
        $validated['shift_ids'] = array_map('intval', $validated['shift_ids'] ?? []);

        return $validated;
    }
}
