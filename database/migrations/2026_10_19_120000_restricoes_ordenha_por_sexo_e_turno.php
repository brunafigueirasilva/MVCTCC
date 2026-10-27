<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Regras de ordenha garantidas também pelo BANCO (não só pela tela/API):
 *
 *  1. turno só pode ser 'Manhã', 'Tarde' ou 'Noite';
 *  2. o turno tem que bater com o horário — Manhã a partir das 04:00,
 *     Tarde a partir das 12:00, Noite a partir das 18:00 (vai até as 03:59);
 *  3. ordenha só em lote com fêmeas (sexo 'Fêmea' ou 'Misto') — lote de
 *     machos nunca tem ordenha;
 *  4. lote que já tem ordenhas não pode ter o sexo trocado para algo sem
 *     fêmeas (o histórico de leite ficaria num lote de machos).
 *
 * 1 e 2 são CHECK (MariaDB 10.2+ / MySQL 8.0.16+); 3 e 4 dependem de outra
 * tabela, então são triggers. As mesmas faixas estão em
 * App\Support\LoteRegras::INICIO_TURNOS_ORDENHA. Em SQLite (testes
 * automatizados) nada disso é criado — lá a validação do Laravel cobre.
 */
return new class extends Migration
{
    private const CHECK_TURNO_VALIDO = "turno IN ('Manhã','Tarde','Noite')";

    private const CHECK_TURNO_HORARIO = "(turno = 'Manhã' AND horario >= '04:00:00' AND horario < '12:00:00')"
        . " OR (turno = 'Tarde' AND horario >= '12:00:00' AND horario < '18:00:00')"
        . " OR (turno = 'Noite' AND (horario >= '18:00:00' OR horario < '04:00:00'))";

    private function bancoSuportado(): bool
    {
        return in_array(DB::getDriverName(), ['mysql', 'mariadb'], true);
    }

    public function up(): void
    {
        if (!$this->bancoSuportado()) {
            return;
        }

        // Ordenha já gravada em lote sem fêmeas: não apagamos dado de
        // ninguém por conta própria — a migration para e mostra quais são.
        $invalidas = DB::table('ordenhas')
            ->join('lotes', 'lotes.id', '=', 'ordenhas.lote_id')
            ->where(fn ($q) => $q->whereNull('lotes.sexo')->orWhereNotIn('lotes.sexo', ['Fêmea', 'Misto']))
            ->select('ordenhas.id', 'lotes.nome_lote', 'lotes.sexo')
            ->get();

        if ($invalidas->isNotEmpty()) {
            $lista = $invalidas->map(fn ($o) => "ordenha #{$o->id} (lote {$o->nome_lote}, sexo " . ($o->sexo ?? 'vazio') . ')')->implode('; ');
            throw new RuntimeException(
                'Existem ordenhas em lotes sem fêmeas, o que a nova regra proíbe: ' . $lista
                . '. Corrija o sexo do lote ou exclua essas ordenhas e rode a migration de novo.'
            );
        }

        // Turno gravado diferente do que o horário indica (antes o turno era
        // escolhido à mão): o horário é o dado real, o turno é recalculado.
        DB::update("UPDATE ordenhas SET turno = CASE
                WHEN horario >= '04:00:00' AND horario < '12:00:00' THEN 'Manhã'
                WHEN horario >= '12:00:00' AND horario < '18:00:00' THEN 'Tarde'
                ELSE 'Noite' END
            WHERE NOT (" . self::CHECK_TURNO_HORARIO . ')');

        DB::statement('ALTER TABLE ordenhas MODIFY turno VARCHAR(10) NOT NULL');
        DB::statement('ALTER TABLE ordenhas ADD CONSTRAINT ordenhas_turno_valido CHECK (' . self::CHECK_TURNO_VALIDO . ')');
        DB::statement('ALTER TABLE ordenhas ADD CONSTRAINT ordenhas_turno_horario CHECK (' . self::CHECK_TURNO_HORARIO . ')');

        $somenteFemeas = "SELECT 1 FROM lotes WHERE id = NEW.lote_id AND sexo IN ('Fêmea','Misto')";
        $mensagemOrdenha = "SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ordenha so pode ser registrada em lote com femeas (sexo Femea ou Misto).'";

        DB::unprepared("CREATE TRIGGER ordenhas_somente_femeas_insert BEFORE INSERT ON ordenhas FOR EACH ROW
            BEGIN
                IF NOT EXISTS ({$somenteFemeas}) THEN {$mensagemOrdenha}; END IF;
            END");

        DB::unprepared("CREATE TRIGGER ordenhas_somente_femeas_update BEFORE UPDATE ON ordenhas FOR EACH ROW
            BEGIN
                IF NOT EXISTS ({$somenteFemeas}) THEN {$mensagemOrdenha}; END IF;
            END");

        DB::unprepared("CREATE TRIGGER lotes_sexo_com_ordenhas BEFORE UPDATE ON lotes FOR EACH ROW
            BEGIN
                IF NOT (NEW.sexo <=> OLD.sexo)
                    AND (NEW.sexo IS NULL OR NEW.sexo NOT IN ('Fêmea','Misto'))
                    AND EXISTS (SELECT 1 FROM ordenhas WHERE lote_id = OLD.id) THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Lote com ordenhas registradas nao pode deixar de ter femeas.';
                END IF;
            END");
    }

    public function down(): void
    {
        if (!$this->bancoSuportado()) {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS lotes_sexo_com_ordenhas');
        DB::unprepared('DROP TRIGGER IF EXISTS ordenhas_somente_femeas_update');
        DB::unprepared('DROP TRIGGER IF EXISTS ordenhas_somente_femeas_insert');
        DB::statement('ALTER TABLE ordenhas DROP CONSTRAINT IF EXISTS ordenhas_turno_horario');
        DB::statement('ALTER TABLE ordenhas DROP CONSTRAINT IF EXISTS ordenhas_turno_valido');
        DB::statement('ALTER TABLE ordenhas MODIFY turno VARCHAR(255) NOT NULL');
    }
};
