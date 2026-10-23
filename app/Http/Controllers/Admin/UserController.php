<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users', ['users' => User::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $this->authorizeManager($request);
        $data = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'type' => ['required', Rule::in(User::ROLES)],
            'password' => 'required|min:6',
        ]);
        User::create(['password' => Hash::make($data['password']), 'active' => true] + $data);

        return back()->with('success', 'User added.');
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeManager($request);
        $data = $request->validate([
            'type' => ['required', Rule::in(User::ROLES)],
            'active' => 'required|boolean',
            'password' => 'nullable|min:6',
        ]);
        if ($user->id === $request->user()->id && (! $data['active'] || $data['type'] !== $user->type)) {
            return back()->withErrors('You cannot change your own role or deactivate yourself.');
        }
        $user->update(['type' => $data['type'], 'active' => $data['active']] + ($data['password'] ? ['password' => Hash::make($data['password'])] : []));

        return back()->with('success', 'User updated.');
    }

    public function destroy(Request $request, User $user)
    {
        $this->authorizeManager($request);
        abort_if($user->id === $request->user()->id, 403, 'You cannot delete yourself.');
        $user->update(['active' => false]);

        return back()->with('success', 'User deactivated.');
    }

    protected function authorizeManager(Request $request): void
    {
        abort_unless($request->user()->isManager(), 403, 'Only managers can manage users.');
    }
}
