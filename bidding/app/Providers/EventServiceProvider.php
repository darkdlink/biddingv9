<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\LicencaCreated;
use App\Events\LicencaUpdated;
use App\Events\LicencaExpiring;
use App\Events\UserRegistered;
use App\Events\LicitacaoSincronizada;
use App\Events\PropostaCreated;
use App\Listeners\SendLicencaWelcomeEmail;
use App\Listeners\SendLicencaStatusUpdateEmail;
use App\Listeners\SendLicencaExpiringNotification;
use App\Listeners\CreateDefaultSegmentsForUser;
use App\Listeners\ProcessLicitacaoRelevancia;
use App\Listeners\SendPropostaConfirmationEmail;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Evento disparado quando um usuário se registra
        UserRegistered::class => [
            CreateDefaultSegmentsForUser::class,
            // Outros listeners
        ],

        // Eventos relacionados a licenças
        LicencaCreated::class => [
            SendLicencaWelcomeEmail::class,
        ],

        LicencaUpdated::class => [
            SendLicencaStatusUpdateEmail::class,
        ],

        LicencaExpiring::class => [
            SendLicencaExpiringNotification::class,
        ],

        // Evento disparado após sincronização de licitações
        LicitacaoSincronizada::class => [
            ProcessLicitacaoRelevancia::class,
        ],

        // Evento disparado quando uma proposta é criada
        PropostaCreated::class => [
            SendPropostaConfirmationEmail::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Agendamento de verificação de licenças prestes a expirar
        // Esta verificação é executada diariamente via scheduler (Kernel.php)
        $this->app->booted(function () {
            if ($this->app->runningInConsole()) {
                // Agendamento de verificação de licenças expiradas será feito no Console Kernel
            }
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
