<?php

namespace App\Http\Controllers;
use App\Models\Address;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'receptor_name'      => 'required|string|max:255',
            'phone'              => 'required|string|max:20',
            'calle_numero'       => 'required|string|max:255',
            'colonia'            => 'required|string|max:255',
            'municipio_alcaldia' => 'required|string|max:255',
            'codigo_postal'      => 'required|string|max:10',
            'estado'             => 'required|string|max:255',
            'referencias'        => 'nullable|string|max:500',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Si es su primera dirección, la hacemos predeterminada
        $isDefault = $user->addresses()->count() === 0 ? true : false;

        $user->addresses()->create([
            'receptor_name'      => $request->receptor_name,
            'phone'              => $request->phone,
            'calle_numero'       => $request->calle_numero,
            'colonia'            => $request->colonia,
            'municipio_alcaldia' => $request->municipio_alcaldia,
            'codigo_postal'      => $request->codigo_postal,
            'estado'             => $request->estado,
            'referencias'        => $request->referencias,
            'is_default'         => $isDefault,
        ]);

        // Regresa al dashboard y activa la pestaña de direcciones
        return redirect()->back()->with('success', '¡Dirección agregada a tu libreta!');
    }
}
