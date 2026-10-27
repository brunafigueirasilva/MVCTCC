<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Quantos cochos o LOTE deve ter — a única coisa que o usuário informa
     * agora. LoteController passa a criar/remover os cochos automaticamente
     * pra bater com este número (ver LoteController::sincronizarCochos()),
     * em vez de exigir um cadastro manual por cocho.
     */
    public function up(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->unsignedInteger('quantidade_cochos')->default(0)->after('quantidade_animais');
        });

        // Backfill: lotes que já têm cocho cadastrado manualmente passam a
        // "declarar" a quantidade que já existe de fato, em vez de nascerem
        // com 0 e o sistema achar que precisa apagar os cochos atuais.
        DB::table('lotes')->update([
            'quantidade_cochos' => DB::raw('(SELECT COUNT(*) FROM cochos WHERE cochos.lote_id = lotes.id)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->dropColumn('quantidade_cochos');
        });
    }
};
