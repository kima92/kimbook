<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Socialite;

//use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;

class RegisteredUserController extends Controller
{
    /**
     * Display the Google login view.
     *
     * @return RedirectResponse
     */
    public function oauth(): RedirectResponse
    {
        $userData = Socialite::driver('google')->user();

        if (!$user = User::firstWhere("email", $userData->getEmail())) {
            $user = User::create([
                'name'              => $userData->getName(),
                'email'             => $userData->getEmail(),
                'password'          => Hash::make(\Illuminate\Support\Str::uuid()->toString()),
                'email_verified_at' => now(),
            ]);

            event(new Registered($user));
        }

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
