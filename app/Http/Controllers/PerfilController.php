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
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect('/mi-cuenta')->with('success', 'Tu contraseña se ha actualizado correctamente.');
    }
}
