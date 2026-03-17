<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('roles.permissions');
        return view('profile.index', compact('user'));
    }

    public function settings()
    {
        $user = Auth::user();
        return view('profile.settings', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $oldData = $user->only(['name', 'last_name', 'email', 'phone', 'id_card', 'address']);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'id_card' => 'nullable|string|unique:users,id_card,' . $user->id,
            'address' => 'nullable|string|max:500',
        ]);
        
        $user->update($request->only('name', 'last_name', 'email', 'phone', 'id_card', 'address'));
        AuditLog::log('updated', $user, $oldData, $user->toArray(), 'Perfil actualizado');
        
        return redirect()->route('profile.settings')->with('success', 'Perfil actualizado correctamente.');
    }

    public function selectAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|string',
        ]);
        $user = Auth::user();
        $avatarFile = $request->avatar;
        if (!file_exists(public_path('build/images/users/' . $avatarFile))) {
            return redirect()->route('profile.settings')->with('error', 'El avatar seleccionado no existe.');
        }
        $user->avatar = $avatarFile;
        $user->save();    
        AuditLog::log('updated', $user, null, null, 'Avatar predefinido seleccionado');
        return redirect()->route('profile.settings')->with('success', 'Avatar actualizado correctamente.');
    }
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $user = Auth::user();
        $oldAvatar = $user->avatar;
        if ($request->hasFile('avatar')) {
            if ($user->avatar && file_exists(public_path('build/images/users/'.$user->avatar))) {
                unlink(public_path('build/images/users/'.$user->avatar));
            }
            
            $fileName = 'avatar-' . $user->id . '-' . time() . '.' . $request->file('avatar')->extension();
            $request->file('avatar')->move(public_path('build/images/users'), $fileName);
            $user->avatar = $fileName;
            $user->save();
            AuditLog::log('updated', $user, ['avatar' => $oldAvatar], ['avatar' => $fileName], 'Avatar actualizado');
        }

        return redirect()->route('profile.settings')->with('success', 'Avatar actualizado correctamente.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();
        if (class_exists('App\Models\AuditLog')) {
            AuditLog::log('updated', $user, null, null, 'Contraseña actualizada');
        }
        return redirect()->route('profile.settings')
            ->with('success', 'Contraseña actualizada correctamente.');
    }

    public function getActivity()
    {
        return response()->json([]);
    }
}