<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cocho extends Model
{
    protected $table = 'cochos';

    /** Capacidade (kg) dos cochos criados sem capacidade informada. */
    public const CAPACIDADE_PADRAO = 100;

    /** Abaixo deste percentual de nível o cocho gera alerta de "Baixo". */
    public const PERCENTUAL_ALERTA = 50;

    // "nome_cocho", "numero" e "lote_id" não vêm de formulário nenhum — são
    // geridos automaticamente por LoteController::sincronizarCochos() a
    // partir de "Quantidade de Cochos" no Lote. Só capacidade/nível/sensor
    // são editáveis por cocho (ver CochoController::update()).
    protected $fillable = [
        'nome_cocho',
        'localizacao',
        'numero',
        'capacidade',
        'nivel_atual',
        'lote_id',
        'sensor',
    ];

    protected function casts(): array
    {
        return [
            'capacidade' => 'float',
            'nivel_atual' => 'float',
        ];
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    /** Sensores IoT instalados (no máximo um de cada tipo). */
    public function sensores()
    {
        return $this->hasMany(Sensor::class);
    }

    public function sensorDistancia()
    {
        return $this->hasOne(Sensor::class)->where('tipo', Sensor::TIPO_DISTANCIA);
    }

    public function sensorBalanca()
    {
        return $this->hasOne(Sensor::class)->where('tipo', Sensor::TIPO_BALANCA);
    }

    public function leiturasIot()
    {
        return $this->hasMany(LeituraIot::class);
    }

    /** Abastecimentos (saídas de estoque) aplicados neste cocho. */
    public function movimentacoes()
    {
        return $this->hasMany(EstoqueMovimentacao::class);
    }

    /**
     * Status IoT do cocho a partir dos sensores ativos: "erro" se algum
     * sensor está com erro, "online" se algum está enviando, "offline" se
     * todos pararam e "sem_leitura" se nenhum nunca enviou (ou não há sensor).
     */
    public function statusSensor(): string
    {
        $status = $this->sensores->where('ativo', true)->map(fn (Sensor $s) => $s->status());

        return match (true) {
            $status->contains(Sensor::STATUS_ERRO) => Sensor::STATUS_ERRO,
            $status->contains(Sensor::STATUS_ONLINE) => Sensor::STATUS_ONLINE,
            $status->contains(Sensor::STATUS_OFFLINE) => Sensor::STATUS_OFFLINE,
            default => Sensor::STATUS_SEM_LEITURA,
        };
    }

    /** Nível atual em % da capacidade (0–100) — null sem capacidade. */
    public function percentualNivel(): ?int
    {
        if ($this->capacidade <= 0) {
            return null;
        }

        return (int) round(min(100, max(0, ($this->nivel_atual / $this->capacidade) * 100)));
    }

    /**
     * Status único usado em todo o sistema (tela de Cochos, alertas do
     * Dashboard e API): "Indefinido" (sem capacidade), "Vazio", "Baixo"
     * (abaixo de PERCENTUAL_ALERTA) ou "OK".
     */
    public function statusAbastecimento(): string
    {
        $percentual = $this->percentualNivel();

        return match (true) {
            $percentual === null => 'Indefinido',
            $this->nivel_atual <= 0 => 'Vazio',
            ($this->nivel_atual / $this->capacidade) * 100 < self::PERCENTUAL_ALERTA => 'Baixo',
            default => 'OK',
        };
    }

    /** true quando o cocho precisa de abastecimento (Vazio ou Baixo). */
    public function precisaAbastecer(): bool
    {
        return in_array($this->statusAbastecimento(), ['Vazio', 'Baixo'], true);
    }

    /**
     * Formato único do cocho na API (listagem, detalhe e resposta de
     * edição/abastecimento): com o resumo do lote e da fazenda que o app lê
     * (models/cocho.dart) e o status calculado no servidor.
     */
    public function dadosApi(): array
    {
        $this->loadMissing([
            'lote:id,nome_lote,fazenda_id,tipo_animal,quantidade_animais',
            'lote.fazenda:id,nome_fazenda',
            'sensores',
        ]);

        $distancia = $this->sensores->firstWhere('tipo', Sensor::TIPO_DISTANCIA);
        $balanca = $this->sensores->firstWhere('tipo', Sensor::TIPO_BALANCA);

        return collect($this->toArray())->except('sensores')->all() + [
            'fazenda_id' => $this->lote?->fazenda_id,
            'percentual_nivel' => $this->percentualNivel(),
            'status_abastecimento' => $this->statusAbastecimento(),
            'alerta_nivel_baixo' => $this->precisaAbastecer(),
            // Resumo IoT (detalhes completos em GET /api/cochos/{id}/iot).
            'quantidade_atual' => (float) $this->nivel_atual,
            'status_sensor' => $this->statusSensor(),
            'sensor_distancia' => $distancia?->codigo,
            'sensor_balanca' => $balanca?->codigo,
            'distancia_vazio_cm' => $distancia?->distancia_vazio_cm,
            'distancia_cheio_cm' => $distancia?->distancia_cheio_cm,
            'ultima_leitura_em' => $this->sensores->max('ultima_leitura_em')?->format('Y-m-d H:i:s'),
        ];
    }
}
