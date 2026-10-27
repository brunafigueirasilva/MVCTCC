<?php

namespace Database\Seeders;

use App\Models\Cadastro;
use App\Models\Cocho;
use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\LoteConsumoSnapshot;
use App\Models\LoteOcorrencia;
use App\Models\Ordenha;
use App\Models\Plano;
use App\Models\Racao;
use App\Services\EstoqueService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Dados de demonstração/teste: 2 usuários (o segundo serve para testar que
 * uma conta não enxerga os dados da outra), fazendas, lotes de várias
 * espécies, cochos, rações com histórico de estoque, planos alimentares,
 * ocorrências, ordenhas e consumo dos últimos 7 dias.
 *
 *   php artisan db:seed --class=AgriFarmDemoSeeder
 *
 * Idempotente: se o usuário demo já existir, não faz nada.
 *   demo@agrifarm.com.br  / Agrifarm@2026
 *   outro@agrifarm.com.br / Agrifarm@2026
 */
class AgriFarmDemoSeeder extends Seeder
{
    public const SENHA = 'Agrifarm@2026';

    public function run(): void
    {
        if (Cadastro::where('email', 'demo@agrifarm.com.br')->exists()) {
            $this->command?->info('Usuário demo já existe — nada a fazer.');
            return;
        }

        DB::transaction(function () {
            $demo = $this->usuario('Usuário Demo', 'demo@agrifarm.com.br', '19990010001');
            $outro = $this->usuario('Outro Produtor', 'outro@agrifarm.com.br', '19990010002');

            $principal = $this->fazenda($demo, 'Fazenda Boa Vista', 'Campinas', 'SP', 120.5);
            $this->fazenda($demo, 'Sítio Santa Luzia', 'Limeira', 'SP', 35);
            $fazendaOutro = $this->fazenda($outro, 'Fazenda do Outro', 'Piracicaba', 'SP', 80);

            $leite = $this->lote($principal, 'Leiteiras A', 'Bovino', 'Leite', 'Fêmea', 40, 520, 3, 'Holandês');
            $corte = $this->lote($principal, 'Engorda B', 'Bovino', 'Corte', 'Macho', 60, 410, 4, 'Nelore');
            $suinos = $this->lote($principal, 'Suínos C', 'Suíno', 'Corte', 'Misto', 120, 85, 2, 'Duroc');
            $aves = $this->lote($principal, 'Poedeiras D', 'Avicultura', 'Ovos', 'Fêmea', 500, 1.8, 2, null);
            $this->lote($principal, 'Equinos E', 'Equino', 'Trabalho', 'Misto', 6, 450, 1, 'Mangalarga');
            $this->lote($fazendaOutro, 'Lote do Outro', 'Bovino', 'Corte', 'Macho', 30, 380, 2, null);

            $estoque = app(EstoqueService::class);
            $racaoLeite = $this->racao($estoque, $demo, $principal, $leite, 'Ração Lactação 22%', 1800, 3000, 120, 2.35, 'Proteína bruta 22%, energia 1,7 Mcal/kg, cálcio, fósforo');
            $racaoCorte = $this->racao($estoque, $demo, $principal, $corte, 'Ração Engorda Plus', 2500, 4000, 210, 1.95, 'Proteína bruta 16%, NDT 72%');
            $racaoSuino = $this->racao($estoque, $demo, $principal, $suinos, 'Ração Suínos Terminação', 600, 1500, 95, 2.10, 'Proteína bruta 15%, lisina 0,9%');
            $racaoAves = $this->racao($estoque, $demo, $principal, $aves, 'Ração Postura', 300, 1000, 60, 2.60, 'Proteína bruta 17%, cálcio 4%');

            // Movimentações extras no histórico (entrada de compra e uma saída manual).
            $estoque->entrada($racaoCorte, 500, ['cadastro_id' => $demo->id, 'observacao' => 'Compra mensal', 'data_movimentacao' => now()->subDays(3)]);
            $estoque->saida($racaoSuino, 40, ['cadastro_id' => $demo->id, 'lote_id' => $suinos->id, 'observacao' => 'Trato manual', 'data_movimentacao' => now()->subDay()]);

            // Abastecimento de cochos pelo mesmo serviço da tela (desconta do estoque).
            $estoque->abastecerCochos($racaoLeite, $leite->cochos->mapWithKeys(fn ($c) => [$c->id => 30])->all(), ['cadastro_id' => $demo->id]);
            $estoque->abastecerCochos($racaoCorte, $corte->cochos->mapWithKeys(fn ($c) => [$c->id => 70])->all(), ['cadastro_id' => $demo->id]);

            foreach ([[$leite, $racaoLeite, 3.0, 3], [$corte, $racaoCorte, 3.5, 2], [$suinos, $racaoSuino, 0.8, 2], [$aves, $racaoAves, 0.12, 2]] as [$lote, $racao, $kg, $freq]) {
                $plano = new Plano([
                    'lote_id' => $lote->id,
                    'racao_id' => $racao->id,
                    'quantidade_recomendada' => $kg,
                    'frequencia_diaria' => $freq,
                    'data_inicio' => now()->subDays(20)->toDateString(),
                    'observacoes' => 'Plano de demonstração.',
                    'ativo' => true,
                ]);
                $plano->fazenda_id = $lote->fazenda_id;
                $plano->save();
            }

            // Consumo diário dos últimos 7 dias (base dos gráficos/relatórios).
            foreach ([[$leite, $racaoLeite, 120], [$corte, $racaoCorte, 210], [$suinos, $racaoSuino, 95], [$aves, $racaoAves, 60]] as [$lote, $racao, $kg]) {
                for ($d = 6; $d >= 0; $d--) {
                    LoteConsumoSnapshot::create([
                        'lote_id' => $lote->id,
                        'racao_id' => $racao->id,
                        'data' => Carbon::today()->subDays($d)->toDateString(),
                        'quantidade_animais' => $lote->quantidade_animais,
                        'consumo_kg' => (int) round($kg * (0.9 + 0.2 * (($d * 7) % 10) / 10)),
                        'status_class' => 'status-ok',
                    ]);
                }
            }

            // Ordenhas (lote de leite, fêmeas) — 7 dias, manhã e tarde.
            for ($d = 6; $d >= 0; $d--) {
                foreach ([['Manhã', '06:00', 310], ['Tarde', '16:00', 280]] as [$turno, $hora, $litros]) {
                    Ordenha::create([
                        'lote_id' => $leite->id,
                        'data' => Carbon::today()->subDays($d)->toDateString(),
                        'horario' => $hora,
                        'turno' => $turno,
                        'quantidade_litros' => $litros + $d * 3,
                        'responsavel_id' => $demo->id,
                    ]);
                }
            }

            // Ocorrências: óbito no lote de corte e parto no lote de leite.
            $this->ocorrencia($corte, 'morte', 1, ['causa' => 'Acidente', 'observacao' => 'Registro de demonstração.']);
            $this->ocorrencia($leite, 'parto', 2, ['observacao' => 'Dois bezerros nascidos.']);
        });

        $this->command?->info('Dados de demonstração criados. Login: demo@agrifarm.com.br / ' . self::SENHA);
    }

    private function usuario(string $nome, string $email, string $telefone): Cadastro
    {
        return Cadastro::create([
            'responsavel' => $nome,
            'email' => $email,
            'telefone' => $telefone,
            'senha' => Hash::make(self::SENHA),
            'email_verificado_em' => now(),
        ]);
    }

    private function fazenda(Cadastro $dono, string $nome, string $cidade, string $uf, float $area): Fazenda
    {
        return $dono->fazendas()->create([
            'nome_fazenda' => $nome,
            'proprietario' => $dono->responsavel,
            'cidade' => $cidade,
            'estado' => $uf,
            'localizacao' => "{$cidade} - {$uf}",
            'area' => $area,
        ]);
    }

    private function lote(Fazenda $fazenda, string $nome, string $tipo, string $finalidade, string $sexo, int $animais, float $peso, int $cochos, ?string $raca): Lote
    {
        $lote = Lote::create([
            'fazenda_id' => $fazenda->id,
            'nome_lote' => $nome,
            'tipo_animal' => $tipo,
            'raca' => $raca,
            'finalidade' => $finalidade,
            'sexo' => $sexo,
            'idade_media' => '1-2 anos',
            'quantidade_animais' => $animais,
            'quantidade_cochos' => $cochos,
            'peso_medio' => $peso,
            'data_entrada' => now()->subDays(60)->toDateString(),
            'status' => 'Ativo',
        ]);

        // Mesma regra da tela: cochos gerados pela quantidade do lote,
        // numerados e com a capacidade padrão do sistema.
        for ($n = 1; $n <= $cochos; $n++) {
            Cocho::create([
                'lote_id' => $lote->id,
                'numero' => $n,
                'nome_cocho' => $nome . ' - ' . str_pad((string) $n, 2, '0', STR_PAD_LEFT),
                'capacidade' => Cocho::CAPACIDADE_PADRAO,
                'nivel_atual' => 0,
            ]);
        }

        return $lote->load('cochos');
    }

    private function racao(EstoqueService $estoque, Cadastro $dono, Fazenda $fazenda, Lote $lote, string $nome, float $saldo, float $capacidade, float $consumo, float $valorKg, string $nutrientes): Racao
    {
        $racao = Racao::create([
            'fazenda_id' => $fazenda->id,
            'nome_racao' => $nome,
            'tipo' => $lote->tipo_animal,
            'sexo' => $lote->sexo,
            'idade' => $lote->idade_media,
            'finalidade' => $lote->finalidade,
            'nutrientes' => $nutrientes,
            'unidade' => 'kg',
            'estoque' => 0,
            'estoque_total' => $capacidade,
            'consumo' => $consumo,
            'valor_kg' => $valorKg,
        ]);

        $estoque->entrada($racao, $saldo, [
            'cadastro_id' => $dono->id,
            'origem' => 'cadastro_racao',
            'observacao' => 'Estoque inicial.',
            'data_movimentacao' => now()->subDays(10),
        ]);

        $lote->update(['racao_id' => $racao->id]);

        return $racao->fresh();
    }

    private function ocorrencia(Lote $lote, string $tipo, int $quantidade, array $extra): void
    {
        $antes = $lote->quantidade_animais;
        $depois = $tipo === 'morte' ? $antes - $quantidade : $antes + $quantidade;

        LoteOcorrencia::create([
            'lote_id' => $lote->id,
            'tipo' => $tipo,
            'quantidade' => $quantidade,
            'quantidade_antes' => $antes,
            'quantidade_depois' => $depois,
            'data_ocorrencia' => now()->subDays(2)->toDateString(),
        ] + $extra);

        $lote->update(['quantidade_animais' => $depois]);
    }
}
