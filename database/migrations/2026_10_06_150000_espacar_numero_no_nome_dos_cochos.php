<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Nome automático dos cochos passa de "Lote A1-01" para "Lote A1 - 01"
 * (ver LoteController::nomeCocho). Renomeia os já existentes que ainda
 * seguem o padrão antigo "<nome do lote>-NN".
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->renomear(fn (string $lote, string $numero) => "{$lote} - {$numero}", '-');
    }

    public function down(): void
    {
        $this->renomear(fn (string $lote, string $numero) => "{$lote}-{$numero}", ' - ');
    }

    private function renomear(callable $novoNome, string $separadorAtual): void
    {
        $cochos = DB::table('cochos')
            ->join('lotes', 'lotes.id', '=', 'cochos.lote_id')
            ->whereNotNull('cochos.numero')
            ->get(['cochos.id', 'cochos.numero', 'cochos.nome_cocho', 'lotes.nome_lote']);

        foreach ($cochos as $cocho) {
            $numero = str_pad((string) $cocho->numero, 2, '0', STR_PAD_LEFT);
            if ($cocho->nome_cocho === $cocho->nome_lote . $separadorAtual . $numero) {
                DB::table('cochos')->where('id', $cocho->id)->update(['nome_cocho' => $novoNome($cocho->nome_lote, $numero)]);
            }
        }
    }
};
