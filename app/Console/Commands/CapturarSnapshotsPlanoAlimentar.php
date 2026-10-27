<?php

namespace App\Console\Commands;

use App\Http\Controllers\PlanoController;
use Illuminate\Console\Command;

/**
 * Grava o snapshot diário de consumo de ração (lote_consumo_snapshots) de
 * TODOS os lotes do sistema — antes esse registro só acontecia quando
 * alguém abria a tela de Plano Alimentar naquele dia (ver
 * PlanoController::capturarSnapshotsDeHoje), deixando buracos no histórico
 * de consumo sempre que a tela ficava um dia sem visita. Agendado em
 * routes/console.php para rodar sozinho todo fim de dia.
 */
class CapturarSnapshotsPlanoAlimentar extends Command
{
    protected $signature = 'plano:capturar-snapshots';

    protected $description = 'Grava o snapshot diário de consumo de ração de todos os lotes (Plano Alimentar)';

    public function handle(PlanoController $planoController): int
    {
        $planoController->capturarSnapshotsGlobal();

        $this->info('Snapshots do dia gravados para todos os lotes.');

        return self::SUCCESS;
    }
}
