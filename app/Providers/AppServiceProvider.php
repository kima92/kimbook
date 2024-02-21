<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use PayMe\Remotisan\Facades\Remotisan;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Remotisan::authWith("super", function(\Illuminate\Http\Request $request) {
            /** @var User $user */
            $user = $request->user('web');
            return $user?->email == 'Kima92@gmail.com';
        });

        Remotisan::setUserIdentifierGetter(function (\Illuminate\Http\Request $request) {
            /** @var User|null $user */
            $user = $request->user("web");
            return $user->name;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
