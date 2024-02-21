<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use OpenAI;
use OpenAI\Client;
use OpenAI\Contracts\ClientContract;
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

        $this->app->singleton(ClientContract::class, static function (): Client {
            $apiKey = config('openai.api_key');
            $organization = config('openai.organization');

            if (! is_string($apiKey) || ($organization !== null && ! is_string($organization))) {
                throw new \RuntimeException("missing API Key");
            }

            return OpenAI::factory()
                ->withApiKey($apiKey)
                ->withOrganization($organization)
                ->withHttpHeader('OpenAI-Beta', 'assistants=v1')
                ->withHttpClient(new \GuzzleHttp\Client(['timeout' => config('openai.request_timeout', 30)]))
                ->make();
        });

        $this->app->alias(ClientContract::class, 'openai');
        $this->app->alias(ClientContract::class, Client::class);
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
