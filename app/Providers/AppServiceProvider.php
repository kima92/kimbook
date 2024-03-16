<?php

namespace App\Providers;

use App\AI\Chat\ChatConversationInterface;
use App\AI\Chat\ChatGPTConversation;
use App\AI\Chat\ClaudeConversation;
use App\Models\Book;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
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
                if (Str::contains($path, "artisan queue:work")) {
                    return;
                }
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
        $this->app->singleton(\App\AI\Claude\Client::class, function (Application $app) {
            throw_unless($apiKey = config("services.anthropic.api_key"), "missing anthropic API Key");

            return new \App\AI\Claude\Client($apiKey);
        });

        $this->app->singleton(ChatConversationInterface::class, function (Application $app) {
            $name = $app['config']->get("services.chatProvider.class");

            return $app->make(match ($name) {
                "claude" => ClaudeConversation::class,
                "gpt"    => ChatGPTConversation::class,
                default  => throw new \RuntimeException("Unknown provider {$name}")
            });
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            1 => User::class,
            2 => Book::class,
            3 => Payment::class,
        ]);

    }
}
