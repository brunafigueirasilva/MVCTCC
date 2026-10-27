<?php

use App\Models\Cocho;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Lotes cujo "Quantidade de Cochos" é maior que os cochos que existem de
 * fato (cochos apagados pela antiga exclusão manual de cocho, antes de eles
 * serem gerados pelo lote). Nesses lotes o Dashboard contava 0 cochos, o
 * "Abastecer cochos do lote" não tinha onde distribuir e a ração do plano
 * não chegava a cocho nenhum — só se corrigiam ao editar o lote.
 *
 * Cria os cochos que faltam, com a mesma numeração/nome/capacidade de
 * LoteController::sincronizarCochos(). Não apaga nada: lote com MAIS
 * cochos que o declarado fica como está.
 */
return new class extends Migration
{
    public function up(): void
    {
        $lotes = DB::table('lotes')
            ->select('lotes.id', 'lotes.nome_lote', 'lotes.quantidade_cochos')
            ->selectRaw('(SELECT COUNT(*) FROM cochos WHERE cochos.lote_id = lotes.id) AS cochos_reais')
            ->get()
            ->filter(fn ($lote) => (int) $lote->quantidade_cochos > (int) $lote->cochos_reais);

        foreach ($lotes as $lote) {
            $ultimoNumero = (int) DB::table('cochos')->where('lote_id', $lote->id)->max('numero');

            for ($i = (int) $lote->cochos_reais; $i < (int) $lote->quantidade_cochos; $i++) {
                $numero = ++$ultimoNumero;

                DB::table('cochos')->insert([
                    'lote_id' => $lote->id,
                    'numero' => $numero,
                    'nome_cocho' => $lote->nome_lote . ' - ' . str_pad((string) $numero, 2, '0', STR_PAD_LEFT),
                    'capacidade' => Cocho::CAPACIDADE_PADRAO,
                    'nivel_atual' => 0,
                    'sensor' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Cochos criados aqui podem já ter abastecimentos/leituras: não há
        // como desfazer sem perder esse histórico.
    }
};
