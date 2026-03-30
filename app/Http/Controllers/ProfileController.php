<?php

namespace App\Http\Controllers;
use App\Models\Address;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\StoreAddressRequest;
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
    public function index(Request $request)
    {
        $pedidos = \App\Models\Order::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard', compact('pedidos'));
    }

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

    public function storeAddress(StoreAddressRequest $request)
    {
        // 🔹 APLICANDO MAP: Solo tomamos lo que definimos en el Request
        $safeData = $request->only([
            'receptor_name',
            'phone',
            'calle_numero',
            'colonia',
            'municipio_alcaldia',
            'estado',
            'codigo_postal',
            'referencias'
        ]);

        // Crear la dirección vinculada al usuario logueado
        Address::create(array_merge($safeData, [
            'user_id' => Auth::id(),
            'is_default' => Address::where('user_id', Auth::id())->count() === 0 // Si es la primera dirección, la marcamos como default
            ]));

        return back()->with('success', 'Dirección agregada correctamente.');
    }
}
