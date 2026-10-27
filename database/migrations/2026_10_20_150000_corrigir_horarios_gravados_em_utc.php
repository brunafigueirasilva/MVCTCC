<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * O Laravel estava com 'timezone' => 'UTC' (config/app.php), mas a máquina,
 * o MariaDB e os usuários estão no horário de Brasília (UTC-3, sem horário
 * de verão desde 2019). Tudo que o servidor gravou com now() ficou 3 h
 * adiantado — e, depois das 21 h, o "hoje" dos gráficos virava amanhã.
 *
 * Com o fuso corrigido (America/Sao_Paulo), esta migration traz os horários
 * já gravados para o horário local:
 *  - colunas TIMESTAMP (created_at, updated_at, email_verificado_em...):
 *    todas preenchidas pelo servidor → −3 h;
 *  - colunas DATETIME de evento (data_movimentacao, data_consumo,
 *    data_hora, ultima_leitura_em, ultimo_erro_em): −3 h, EXCETO quando o
 *    valor foi um DIA escolhido pela pessoa (hora 00:00:00 e diferente do
 *    created_at) — esse já está no horário local;
 *  - colunas DATE/TIME digitadas (data de entrada, ordenha, plano,
 *    nascimento): não mudam.
 * Cada tabela é ajustada num UPDATE só (colunas com ON UPDATE
 * CURRENT_TIMESTAMP, como codigos_verificacao.expira_em, não disparam).
 * Só MySQL/MariaDB; banco vazio (migrate:fresh) não tem o que corrigir.
 */
return new class extends Migration
{
    private const HORAS = 3;

    /** Colunas DATETIME que podem conter um dia escolhido pela pessoa. */
    private const EVENTOS = ['data_movimentacao', 'data_consumo', 'data_hora'];

    private function bancoSuportado(): bool
    {
        return in_array(DB::getDriverName(), ['mysql', 'mariadb'], true);
    }

    private function colunasPorTabela(): array
    {
        $linhas = DB::select(
            "SELECT table_name AS tabela, column_name AS coluna, data_type AS tipo
               FROM information_schema.columns
              WHERE table_schema = DATABASE() AND data_type IN ('timestamp', 'datetime')
              ORDER BY table_name, ordinal_position"
        );

        $porTabela = [];
        foreach ($linhas as $l) {
            $porTabela[$l->tabela][$l->coluna] = $l->tipo;
        }

        return $porTabela;
    }

    private function ajustar(string $sinal): void
    {
        if (!$this->bancoSuportado()) {
            return;
        }

        foreach ($this->colunasPorTabela() as $tabela => $colunas) {
            $temCriacao = isset($colunas['created_at']);
            $sets = [];

            // Eventos primeiro: o MySQL avalia o SET da esquerda para a
            // direita, então a condição ainda enxerga o created_at original.
            foreach ($colunas as $coluna => $tipo) {
                if (!in_array($coluna, self::EVENTOS, true)) {
                    continue;
                }
                $deslocado = "`{$coluna}` {$sinal} INTERVAL " . self::HORAS . ' HOUR';
                $sets[] = $temCriacao
                    ? "`{$coluna}` = IF(`{$coluna}` IS NOT NULL AND TIME(`{$coluna}`) = '00:00:00' AND ABS(TIMESTAMPDIFF(SECOND, `{$coluna}`, `created_at`)) > 5, `{$coluna}`, {$deslocado})"
                    : "`{$coluna}` = {$deslocado}";
            }

            foreach ($colunas as $coluna => $tipo) {
                if (in_array($coluna, self::EVENTOS, true)) {
                    continue;
                }
                $sets[] = "`{$coluna}` = `{$coluna}` {$sinal} INTERVAL " . self::HORAS . ' HOUR';
            }

            // NULL - INTERVAL continua NULL; uma instrução por tabela.
            DB::statement("UPDATE `{$tabela}` SET " . implode(', ', $sets));
        }
    }

    public function up(): void
    {
        $this->ajustar('-');
    }

    public function down(): void
    {
        $this->ajustar('+');
    }
};
