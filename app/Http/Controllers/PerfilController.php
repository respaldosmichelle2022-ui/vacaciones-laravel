<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('perfil.index', compact('user'));
    }

    public function actualizar(Request $request)
    {
        $user = User::findOrFail(auth()->id());

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'max:255',
                'unique:users,email,' . $user->id,
                function ($attribute, $value, $fail) {
                    $isEmail = filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
                    $isNombre = preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u', $value) === 1;
                    $isNombreNumero = preg_match('/^(?=.*[a-zA-ZáéíóúÁÉÍÓÚñÑ])(?=.*\d)[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\d]+$/u', $value) === 1;

                    if (!$isEmail && !$isNombre && !$isNombreNumero) {
                        $fail('El formato de usuario/correo no es válido. Debe ser un correo electrónico válido, solo letras (nombre), o letras y números (nombre + número).');
                    }
                }
            ],
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect('/mi-cuenta')->with('success', 'Tu perfil y contraseña se han actualizado correctamente.');
    }
}
