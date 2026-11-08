<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    /**
     * Display a listing of all employees and their roles/permissions.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $roleSlug = $request->input('role');
        $status = $request->input('status');

        $query = User::with('roles')
            ->withCount(['managedClients', 'assignedTickets']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($roleSlug) {
            $query->whereHas('roles', function ($q) use ($roleSlug) {
                $q->where('slug', $roleSlug);
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('is_active', (bool) $status);
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        // Metrics for administrative cards
        $allUsers = User::with('roles')->get();
        $metrics = [
            'total' => $allUsers->count(),
            'active' => $allUsers->where('is_active', true)->count(),
            'inactive' => $allUsers->where('is_active', false)->count(),
            'super_admins' => $allUsers->filter(fn($u) => $u->isSuperAdmin())->count(),
            'admins' => $allUsers->filter(fn($u) => $u->hasRole('admin'))->count(),
            'account_managers' => $allUsers->filter(fn($u) => $u->hasRole('account-manager'))->count(),
            'specialists' => $allUsers->filter(fn($u) => !$u->isAdmin())->count(),
        ];

        $roles = Role::where('slug', '!=', 'super-admin')->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'metrics', 'roles'));
    }

    /**
     * Show form to create a new employee.
     */
    public function create()
    {
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->isSuperAdmin();

        // Super Admin can assign 'admin'; standard admin cannot
        $rolesQuery = Role::where('slug', '!=', 'super-admin');
        if (!$isSuperAdmin) {
            $rolesQuery->where('slug', '!=', 'admin');
        }

        $roles = $rolesQuery->orderBy('name')->get();

        return view('admin.users.create', compact('roles', 'isSuperAdmin'));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->isSuperAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'password' => ['required', 'string', Password::defaults()],
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'boolean',
        ]);

        $selectedRoleIds = $validated['roles'] ?? [];
        $selectedRoles = Role::whereIn('id', $selectedRoleIds)->get();

        // Strict Hierarchy Check:
        // Attempting to assign 'admin' role requires Super Admin privileges
        if ($selectedRoles->contains('slug', 'admin') && !$isSuperAdmin) {
            abort(403, 'Acceso Denegado: Solo el Super Administrador puede crear o promover a otro Administrador.');
        }

        // Nobody can create a second super-admin via this form
        if ($selectedRoles->contains('slug', 'super-admin')) {
            abort(403, 'Acceso Denegado: No se pueden crear roles adicionales de Super Administrador.');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $user->roles()->sync($selectedRoleIds);

        return redirect()->route('admin.users.index')
            ->with('success', "Empleado '{$user->name}' registrado exitosamente con sus roles asignados.");
    }

    /**
     * Show form to edit employee details and permissions.
     */
    public function edit(User $user)
    {
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->isSuperAdmin();

        // Security restriction: standard admin cannot edit Super Admin or another Admin
        if ($user->isSuperAdmin() && $currentUser->id !== $user->id) {
            abort(403, 'Acceso Denegado: No tienes autorización para modificar la cuenta del Super Administrador.');
        }

        if ($user->isAdmin() && !$isSuperAdmin && $currentUser->id !== $user->id) {
            abort(403, 'Acceso Denegado: Solo el Super Administrador puede modificar los permisos de otros Administradores.');
        }

        $user->load('roles');

        $rolesQuery = Role::where('slug', '!=', 'super-admin');
        if (!$isSuperAdmin) {
            // Standard admin cannot view/toggle the 'admin' role checkbox
            $rolesQuery->where('slug', '!=', 'admin');
        }

        $roles = $rolesQuery->orderBy('name')->get();
        $userRoleIds = $user->roles->pluck('id')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'userRoleIds', 'isSuperAdmin'));
    }

    /**
     * Update employee details and sync roles/permissions.
     */
    public function update(Request $request, User $user)
    {
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->isSuperAdmin();

        // Security check
        if ($user->isSuperAdmin() && $currentUser->id !== $user->id) {
            abort(403, 'Acceso Denegado: No tienes autorización para modificar la cuenta del Super Administrador.');
        }

        if ($user->isAdmin() && !$isSuperAdmin && $currentUser->id !== $user->id) {
            abort(403, 'Acceso Denegado: Solo el Super Administrador puede modificar los permisos de otros Administradores.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:users,email,{$user->id}",
            'phone' => 'nullable|string|max:50',
            'password' => ['nullable', 'string', Password::defaults()],
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'boolean',
        ]);

        $selectedRoleIds = $validated['roles'] ?? [];
        $selectedRoles = Role::whereIn('id', $selectedRoleIds)->get();

        $previouslyHadAdmin = $user->hasRole('admin');
        $nowWantsAdmin = $selectedRoles->contains('slug', 'admin');

        // Hierarchy rule: Promoting or revoking Admin privilege requires Super Admin
        if (($previouslyHadAdmin !== $nowWantsAdmin) && !$isSuperAdmin) {
            abort(403, 'Acceso Denegado: Solo el Super Administrador puede otorgar o revocar privilegios de Administrador.');
        }

        // Maintain super-admin role if editing oneself as super-admin
        if ($user->isSuperAdmin()) {
            $superAdminRoleId = Role::where('slug', 'super-admin')->value('id');
            if ($superAdminRoleId && !in_array($superAdminRoleId, $selectedRoleIds)) {
                $selectedRoleIds[] = $superAdminRoleId;
            }
        }

        $userData = [
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);
        $user->roles()->sync($selectedRoleIds);

        return redirect()->route('admin.users.index')
            ->with('success', "Permisos y datos de '{$user->name}' actualizados correctamente.");
    }

    /**
     * Quick toggle employee status (active / inactive).
     */
    public function toggleStatus(User $user)
    {
        $currentUser = auth()->user();

        if ($user->isSuperAdmin()) {
            return back()->with('error', 'No se puede desactivar la cuenta del Super Administrador.');
        }

        if ($currentUser->id === $user->id) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta mientras estás en sesión.');
        }

        if ($user->isAdmin() && !$currentUser->isSuperAdmin()) {
            abort(403, 'Solo el Super Administrador puede suspender a otro Administrador.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $statusText = $user->is_active ? 'activado' : 'desactivado';
        return back()->with('success', "El usuario '{$user->name}' ha sido {$statusText} correctamente.");
    }

    /**
     * Delete an employee (Super Admin only).
     */
    public function destroy(User $user)
    {
        $currentUser = auth()->user();

        if (!$currentUser->isSuperAdmin()) {
            abort(403, 'Acceso Denegado: Solo el Super Administrador puede eliminar registros de empleados.');
        }

        if ($user->isSuperAdmin() || $currentUser->id === $user->id) {
            return back()->with('error', 'No se puede eliminar al Super Administrador ni a tu propia cuenta.');
        }

        $userName = $user->name;
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Empleado '{$userName}' eliminado del sistema.");
    }
}
