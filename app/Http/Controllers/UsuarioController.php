<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Empleado;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $users = User::with('empleado')->get();
        return view('usuarios.index', compact('users'));
    }

    public function crear()
    {
        $sitios = Empleado::select('sitio')
            ->distinct()
            ->whereNotNull('sitio')
            ->where('sitio', '!=', '')
            ->orderBy('sitio')
            ->pluck('sitio');
        return view('usuarios.crear', compact('sitios'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'max:255',
                'unique:users,email',
                function ($attribute, $value, $fail) {
                    $isEmail = filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
                    $isNombre = preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u', $value) === 1;
                    $isNombreNumero = preg_match('/^(?=.*[a-zA-ZáéíóúÁÉÍÓÚñÑ])(?=.*\d)[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\d]+$/u', $value) === 1;

                    if (!$isEmail && !$isNombre && !$isNombreNumero) {
                        $fail('El formato de usuario/correo no es válido. Debe ser un correo electrónico válido, solo letras (nombre), o letras y números (nombre + número).');
                    }
                }
            ],
            'password' => 'required|string|min:6',
            'role' => 'required|in:administrador,empleado,solo_lectura',
            'sitio' => 'nullable|string|max:255',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'sitio' => in_array($request->role, ['empleado', 'solo_lectura']) ? $request->sitio : null,
            'empleado_id' => null,
        ]);

        return redirect('/usuarios')->with('success', 'Usuario creado correctamente.');
    }

    public function editar($id)
    {
        $user = User::findOrFail($id);
        $sitios = Empleado::select('sitio')
            ->distinct()
            ->whereNotNull('sitio')
            ->where('sitio', '!=', '')
            ->orderBy('sitio')
            ->pluck('sitio');
        return view('usuarios.editar', compact('user', 'sitios'));
    }

    public function actualizar(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'max:255',
                'unique:users,email,' . $id,
                function ($attribute, $value, $fail) {
                    $isEmail = filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
                    $isNombre = preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u', $value) === 1;
                    $isNombreNumero = preg_match('/^(?=.*[a-zA-ZáéíóúÁÉÍÓÚñÑ])(?=.*\d)[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\d]+$/u', $value) === 1;

                    if (!$isEmail && !$isNombre && !$isNombreNumero) {
                        $fail('El formato de usuario/correo no es válido. Debe ser un correo electrónico válido, solo letras (nombre), o letras y números (nombre + número).');
                    }
                }
            ],
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:administrador,empleado,solo_lectura',
            'sitio' => 'nullable|string|max:255',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'sitio' => in_array($request->role, ['empleado', 'solo_lectura']) ? $request->sitio : null,
            'empleado_id' => null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect('/usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

    public function eliminar($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting oneself
        if (auth()->id() == $user->id) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();

        return redirect('/usuarios')->with('success', 'Usuario eliminado correctamente.');
    }

    public function exportarExcel()
    {
        $sitio = auth()->user()->sitio;
        $query = User::query();
        if ($sitio) {
            $query->where('sitio', $sitio);
        }
        $users = $query->get();

        $filename = "reporte_usuarios_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Nombre Completo', 'Usuario / Email', 'Rol', 'Sitio Vinculado'];

        $callback = function() use($users, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            foreach ($users as $u) {
                fputcsv($file, [
                    $u->name,
                    $u->email,
                    $u->role,
                    $u->sitio ?? 'Ninguno (Acceso Global)'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
