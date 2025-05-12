<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\SincronizarLicitacoes::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Executar sincronização todos os dias às 7h da manhã
        $schedule->command('licitacoes:sincronizar --paginas=10')
                 ->dailyAt('07:00')
                 ->appendOutputTo(storage_path('logs/sincronizacao-licitacoes.log'));
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
