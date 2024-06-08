<?php

namespace App\Providers;

use App\AI\Art\ReplicateInstantId;
use App\AI\Art\ReplicatePhotomakerStyle;
use App\AI\Art\ReplicateSdxlLightning4Step;
use App\AI\Chat\ChatConversationInterface;
use App\AI\Chat\ChatGPTConversation;
use App\AI\Chat\ClaudeConversation;
use App\AI\Chat\OllamaLlama3Conversation;
use App\AI\Chat\ReplicateLlama3_70B_InstructConversation;
use App\AI\Chat\ReplicateLlama3Conversation;
use App\Models\Book;
use App\Models\Payment;
use App\Models\User;
use BenBjurstrom\Replicate\Replicate;
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
                "llama3" => ReplicateLlama3Conversation::class,
                "llama3-70b-instruct" => ReplicateLlama3_70B_InstructConversation::class,
                "ollama-llama3" => OllamaLlama3Conversation::class,
                default  => throw new \RuntimeException("Unknown provider {$name}")
            });
        });

        $this->app->singleton(Replicate::class, function (Application $app) {
            return new Replicate(
                apiToken: config('services.replicate.api_key'),
            );
        });

        $this->app->bind(ReplicateInstantId::class, function (Application $app) {
            return new ReplicateInstantId($app->make(Replicate::class));
        });

        $this->app->bind(ReplicateSdxlLightning4Step::class, function (Application $app) {
            return new ReplicateSdxlLightning4Step($app->make(Replicate::class));
        });

        $this->app->bind(ReplicatePhotomakerStyle::class, function (Application $app) {
            return new ReplicatePhotomakerStyle($app->make(Replicate::class));
        });

        $this->app->bind(ReplicateLlama3Conversation::class, function (Application $app) {
            return new ReplicateLlama3Conversation($app->make(Replicate::class));
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
