<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Admin::with('roles')->latest()->paginate(25);

        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        $roles = Role::where('guard', 'admin')
            ->where('name', '!=', 'super_admin')
            ->get();

        return view('admin.staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:admins,email'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $role = Role::findOrFail($data['role_id']);

        // Prevent escalation to super_admin
        abort_if($role->name === 'super_admin' && ! auth('admin')->user()->isSuperAdmin(), 403);

        $admin = new Admin;
        $admin->name = $data['name'];
        $admin->email = $data['email'];
        $admin->password = Hash::make($data['password']);
        $admin->status = $data['status'];
        $admin->save();

        $admin->roles()->sync([$role->id]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member created.');
    }

    public function edit(Admin $staff)
    {
        $roles = Role::where('guard', 'admin')
            ->where('name', '!=', 'super_admin')
            ->get();

        return view('admin.staff.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, Admin $staff)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:admins,email,'.$staff->id],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $role = Role::findOrFail($data['role_id']);

        abort_if($role->name === 'super_admin' && ! auth('admin')->user()->isSuperAdmin(), 403);

        $staff->name = $data['name'];
        $staff->email = $data['email'];
        $staff->status = $data['status'];
        $staff->save();

        $staff->roles()->sync([$role->id]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated.');
    }

    public function destroy(Admin $staff)
    {
        abort_if($staff->isSuperAdmin(), 403);
        abort_if($staff->id === auth('admin')->id(), 403);

        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted.');
    }
}
