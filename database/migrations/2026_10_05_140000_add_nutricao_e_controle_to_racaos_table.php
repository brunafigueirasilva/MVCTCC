<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Características nutricionais e de controle da ração (tela "Tipos de
 * Rações" do app). Só ADICIONA colunas, todas opcionais — nenhum dado
 * existente muda. "nutrientes" (já existente) passa a ser "outros
 * nutrientes". "status" nasce como 'ativa' para todas as rações atuais.
 */
return new class extends Migration
{
    /** @var array<string, callable(Blueprint): void> */
    private function colunas(): array
    {
        return [
            'proteina_percentual' => fn (Blueprint $t) => $t->decimal('proteina_percentual', 5, 2)->nullable(),
            'energia_mcal_kg' => fn (Blueprint $t) => $t->decimal('energia_mcal_kg', 6, 2)->nullable(),
            'fibra_percentual' => fn (Blueprint $t) => $t->decimal('fibra_percentual', 5, 2)->nullable(),
            'minerais' => fn (Blueprint $t) => $t->string('minerais', 255)->nullable(),
            'estoque_minimo' => fn (Blueprint $t) => $t->decimal('estoque_minimo', 10, 2)->nullable(),
            'status' => fn (Blueprint $t) => $t->string('status', 20)->default('ativa'),
            'observacoes' => fn (Blueprint $t) => $t->text('observacoes')->nullable(),
        ];
    }

    public function up(): void
    {
        Schema::table('racaos', function (Blueprint $table) {
            foreach ($this->colunas() as $nome => $criar) {
                if (!Schema::hasColumn('racaos', $nome)) {
                    $criar($table);
                }
            }
        });
    }

    public function down(): void
    {
        $existentes = array_values(array_filter(
            array_keys($this->colunas()),
            fn ($nome) => Schema::hasColumn('racaos', $nome)
        ));

        if ($existentes) {
            Schema::table('racaos', fn (Blueprint $table) => $table->dropColumn($existentes));
        }
    }
};
