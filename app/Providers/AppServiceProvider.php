<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
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
        App::terminating(function() {
            if (app()->runningInConsole()) {
                $method = "Console";
                $path = join(" ", $_SERVER['argv']);
            } else {
                $method = Request::method();

                if ($queryString = Arr::get($_SERVER, "QUERY_STRING")) {
                    $path = Request::path() . "?" . $queryString;
                } else {
                    $path = Request::getRequestUri();
                }
            }

            $runtime = defined('LARAVEL_START') ? round((microtime(true) - LARAVEL_START), 4) : 0;
            \Log::debug("[Runtime][{$method}] {$path} {$runtime}");
        });

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
