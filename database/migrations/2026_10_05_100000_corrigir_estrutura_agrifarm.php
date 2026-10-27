<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Revisão da estrutura do banco (Usuário → Fazenda → Lote → Cocho;
 * Fazenda → Ração → Estoque; Fazenda → Plano Alimentar):
 *
 *  1. racaos: estoque/estoque_total/consumo eram INT, mas o abastecimento de
 *     cochos desconta quantidades fracionadas (ex.: 12,5 kg) — a parte
 *     decimal se perdia a cada operação. Viram DECIMAL(10,2). Ganham também
 *     "nutrientes" e "unidade" (opcionais) e a FK com a fazenda passa a ser
 *     CASCADE (com SET NULL, excluir a fazenda deixava rações órfãs que
 *     nenhuma conta enxerga mais).
 *  2. cochos: capacidade padrão 100 (regra do sistema).
 *  3. estoque_movimentacoes (nova): histórico de entradas/saídas de ração.
 *  4. planos: racao_id/lote_id eram VARCHAR sem FK e a tabela nunca foi
 *     usada — vira o Plano Alimentar de verdade (fazenda, lote, ração,
 *     quantidade, frequência, período, observações), com FKs.
 *  5. Remove tabelas sem uso: "animals" (cadastro individual de animais,
 *     que o sistema não tem — o manejo é por lote), "relatorios" (relatórios
 *     são calculados) e "estoques" (duplicava o estoque das rações; o único
 *     dado que não existia na ração — a capacidade — é copiado antes).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ---------------------------------------------------------- 1. racaos
        if (Schema::hasTable('estoques')) {
            // Capacidade que só existia em "estoques" vai pra ração antes de
            // a tabela ser removida.
            foreach (DB::table('estoques')->get() as $estoque) {
                DB::table('racaos')
                    ->where('id', $estoque->racao_id)
                    ->whereNull('estoque_total')
                    ->update(['estoque_total' => $estoque->capacidade]);
            }
        }

        // Rações sem fazenda (sobras do SET NULL): recupera a fazenda pelo
        // lote que usa a ração, quando houver.
        foreach (DB::table('racaos')->whereNull('fazenda_id')->pluck('id') as $racaoId) {
            $fazendaId = DB::table('lotes')->where('racao_id', $racaoId)->value('fazenda_id');
            if ($fazendaId) {
                DB::table('racaos')->where('id', $racaoId)->update(['fazenda_id' => $fazendaId]);
            }
        }

        Schema::table('racaos', function (Blueprint $table) {
            $table->decimal('estoque', 10, 2)->default(0)->change();
            $table->decimal('estoque_total', 10, 2)->nullable()->change();
            $table->decimal('consumo', 10, 2)->default(0)->change();
        });

        Schema::table('racaos', function (Blueprint $table) {
            if (!Schema::hasColumn('racaos', 'nutrientes')) {
                $table->text('nutrientes')->nullable()->after('finalidade');
            }
            if (!Schema::hasColumn('racaos', 'unidade')) {
                $table->string('unidade', 10)->default('kg')->after('nutrientes');
            }
        });

        Schema::table('racaos', function (Blueprint $table) {
            $table->dropForeign(['fazenda_id']);
        });
        Schema::table('racaos', function (Blueprint $table) {
            $table->foreign('fazenda_id')->references('id')->on('fazendas')->cascadeOnDelete();
        });

        // ---------------------------------------------------------- 2. cochos
        Schema::table('cochos', function (Blueprint $table) {
            $table->decimal('capacidade', 10, 2)->default(100)->change();
        });

        // ------------------------------------------- 3. estoque_movimentacoes
        if (!Schema::hasTable('estoque_movimentacoes')) {
            Schema::create('estoque_movimentacoes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fazenda_id')->constrained('fazendas')->cascadeOnDelete();
                $table->foreignId('racao_id')->constrained('racaos')->cascadeOnDelete();
                $table->foreignId('cadastro_id')->nullable()->constrained('cadastros')->nullOnDelete();
                $table->foreignId('lote_id')->nullable()->constrained('lotes')->nullOnDelete();
                $table->foreignId('cocho_id')->nullable()->constrained('cochos')->nullOnDelete();
                // entrada | saida | ajuste
                $table->string('tipo', 10);
                // manual | abastecimento_cocho | cadastro_racao | edicao_racao
                $table->string('origem', 30)->default('manual');
                $table->decimal('quantidade', 10, 2);
                $table->decimal('saldo_anterior', 10, 2);
                $table->decimal('saldo_posterior', 10, 2);
                $table->decimal('valor_unitario', 10, 2)->nullable();
                $table->string('observacao')->nullable();
                $table->dateTime('data_movimentacao');
                $table->timestamps();

                $table->index(['fazenda_id', 'data_movimentacao']);
                $table->index(['racao_id', 'data_movimentacao']);
            });
        }

        // ---------------------------------------------------------- 4. planos
        if (Schema::hasTable('planos') && !Schema::hasColumn('planos', 'fazenda_id')) {
            if (DB::table('planos')->count() > 0) {
                // Nunca acontece hoje (a tabela nunca foi usada), mas não
                // apaga dado nenhum se acontecer: guarda com outro nome.
                Schema::rename('planos', 'planos_legado');
            } else {
                Schema::drop('planos');
            }
        }

        if (!Schema::hasTable('planos')) {
            Schema::create('planos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fazenda_id')->constrained('fazendas')->cascadeOnDelete();
                $table->foreignId('lote_id')->constrained('lotes')->cascadeOnDelete();
                $table->foreignId('racao_id')->constrained('racaos')->cascadeOnDelete();
                // kg por animal por dia
                $table->decimal('quantidade_recomendada', 10, 2);
                // tratos por dia
                $table->unsignedTinyInteger('frequencia_diaria')->default(1);
                $table->date('data_inicio');
                $table->date('data_fim')->nullable();
                $table->text('observacoes')->nullable();
                $table->boolean('ativo')->default(true);
                $table->timestamps();

                $table->index(['fazenda_id', 'ativo']);
                $table->index(['lote_id', 'ativo']);
            });
        }

        // ------------------------------------------------ 5. tabelas sem uso
        if (Schema::hasTable('animals') && DB::table('animals')->count() === 0) {
            Schema::drop('animals');
        }
        if (Schema::hasTable('relatorios') && DB::table('relatorios')->count() === 0) {
            Schema::drop('relatorios');
        }
        Schema::dropIfExists('estoques');

        // ---------------------------------------------------------- índices
        Schema::table('lotes', function (Blueprint $table) {
            $table->index(['fazenda_id', 'status'], 'lotes_fazenda_status_index');
        });
    }

    public function down(): void
    {
        // O MySQL passa a usar o índice composto pra FK de fazenda_id (e
        // descarta o índice implícito dela) — recria um índice simples antes
        // de remover o composto, senão o DROP é recusado (erro 1553).
        Schema::table('lotes', function (Blueprint $table) {
            $table->index('fazenda_id', 'lotes_fazenda_id_index');
        });
        Schema::table('lotes', function (Blueprint $table) {
            $table->dropIndex('lotes_fazenda_status_index');
        });

        Schema::dropIfExists('planos');
        Schema::create('planos', function (Blueprint $table) {
            $table->id();
            $table->string('racao_id');
            $table->string('lote_id');
            $table->decimal('quantidade', 10, 2);
            $table->dateTime('horario');
            $table->string('consumo_ideal');
            $table->timestamps();
        });

        Schema::dropIfExists('estoque_movimentacoes');

        Schema::table('cochos', function (Blueprint $table) {
            $table->decimal('capacidade', 10, 2)->default(0)->change();
        });

        Schema::table('racaos', function (Blueprint $table) {
            $table->dropForeign(['fazenda_id']);
        });
        Schema::table('racaos', function (Blueprint $table) {
            $table->foreign('fazenda_id')->references('id')->on('fazendas')->nullOnDelete();
            $table->dropColumn(['nutrientes', 'unidade']);
            $table->integer('estoque')->change();
            $table->unsignedInteger('estoque_total')->nullable()->change();
            $table->integer('consumo')->change();
        });
    }
};
