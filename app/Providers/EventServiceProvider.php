<?php

namespace App\Providers;

use App\Listeners\TelegramNotificationsListener;
use App\Utils\Telegram;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobQueued;
use Illuminate\Queue\Jobs\JobName;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Log;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    protected $subscribe = [
        TelegramNotificationsListener::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {

        Event::listen(function (JobQueued $event) {
            Log::debug("[Queue][push] Queued job", (array)$event->job);
        });

        Queue::before(function (JobProcessing $event) {
            $job = $event->job;
            $jobName = JobName::resolve($job->getName(), $job->payload());

            $msg = "[Queue][before] Job [{$jobName}] {$job->getJobId()} running on ({$job->getQueue()}), attempt #{$job->attempts()}, queueing timestamp " . (data_get($job->payload(), "queued_at"));
            Log::debug($msg);
            (new Telegram())->send($msg);
        });

        Queue::after(function (JobProcessed $event) {
            Log::debug("[Queue][after] Job {$event->job->getJobId()} processed. isDeletedOrReleased? (".json_encode($event->job->isDeletedOrReleased()).")");
        });

        Queue::failing(function (JobFailed $event) {
            $job = $event->job;

            $msg = "[Queue][failing] Job {$job->getJobId()} running on ({$job->getQueue()}) *failed*!\n" .
                   "```Error Message: {$event->exception->getMessage()}\nAdditional Data: \n{$job->getRawBody()}```";
            Log::critical($msg);
            (new Telegram())->send($msg);
        });

        Queue::exceptionOccurred(function (JobExceptionOccurred $event) {
            $job = $event->job;

            $msg = "[Queue][exceptionOccurred] Job {$job->getJobId()} exception: {$event->exception->getMessage()}\n{$event->exception->getTraceAsString()}";
            Log::error($msg);
            (new Telegram())->send($msg);

            if (! $job->isDeleted() && ! $job->isReleased() && ! $job->hasFailed()) {
                Log::debug("[Queue][exceptionOccurred] Job {$job->getJobId()} released back to the queue");
            } else {
                Log::debug("[Queue][exceptionOccurred] Job {$job->getJobId()} deleted");
            }
        });
    }
}
