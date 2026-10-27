<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabelas-base do AgriFarm (cadastros, fazendas, lotes, racaos, cochos).
 *
 * Elas só existiam no bd_tcc.sql — nenhuma migration as criava, então um
 * "php artisan migrate" num banco vazio quebrava logo na primeira migration
 * "add_..._to_cadastros_table". Esta migration roda antes de todas as outras
 * e cria cada tabela no formato ORIGINAL (as colunas adicionadas depois
 * continuam vindo das migrations "add_*" de sempre, na mesma ordem).
 *
 * Em bancos que já têm essas tabelas (importados do bd_tcc.sql) cada bloco
 * é ignorado — Schema::hasTable() — então nada muda nesses bancos.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cadastros')) {
            Schema::create('cadastros', function (Blueprint $table) {
                $table->id();
                $table->string('responsavel', 100);
                $table->string('email', 100)->unique();
                $table->string('telefone', 14);
                $table->string('senha');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('fazendas')) {
            Schema::create('fazendas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cadastro_id')->constrained('cadastros')->cascadeOnDelete();
                $table->string('nome_fazenda', 100);
                $table->string('localizacao')->nullable();
                $table->decimal('area', 10, 2)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('racaos')) {
            Schema::create('racaos', function (Blueprint $table) {
                $table->id();
                $table->string('tipo', 225)->nullable();
                $table->string('nome_racao', 100);
                $table->integer('estoque');
                $table->integer('consumo');
                $table->string('fornecedor', 100)->nullable();
                $table->date('reposicao')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lotes')) {
            Schema::create('lotes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fazenda_id')->constrained('fazendas')->cascadeOnDelete();
                $table->string('nome_lote', 100);
                $table->string('tipo_animal', 50);
                $table->integer('quantidade_animais')->default(0);
                $table->decimal('peso_medio', 10, 2)->default(0);
                $table->date('data_entrada')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cochos')) {
            Schema::create('cochos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lote_id')->constrained('lotes')->cascadeOnDelete();
                $table->string('nome_cocho');
                $table->decimal('capacidade', 10, 2);
                $table->decimal('nivel_atual', 10, 2)->default(0);
                $table->string('sensor');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cochos');
        Schema::dropIfExists('lotes');
        Schema::dropIfExists('racaos');
        Schema::dropIfExists('fazendas');
        Schema::dropIfExists('cadastros');
    }
};
