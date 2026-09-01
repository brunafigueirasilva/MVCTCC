<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Regras de negócio do módulo de Animal/Parto/Lactação/Ordenha, isoladas do
 * Controller de propósito — mesmo padrão de App\Support\LoteRegras: tanto a
 * validação do back-end quanto os <select> da view leem daqui, então nunca
 * ficam dessincronizados.
 */
class AnimalRegras
{
    public const SEXOS = ['Macho', 'Fêmea'];

    public const STATUS = ['Ativo', 'Inativo', 'Vendido', 'Morto'];

    /**
     * Ciclo reprodutivo do animal. "Vazia" é o estado inicial (nunca teve
     * parto, ou já devolveu do período seco sem uma nova prenhez ainda).
     */
    public const STATUS_REPRODUTIVO = ['Vazia', 'Prenhe', 'Em lactação', 'Período seco'];

    public const TIPOS_PARTO = ['Normal', 'Assistido', 'Cesárea', 'Aborto'];

    /**
     * Calculados a partir das datas — nunca gravados na coluna "status" do
     * parto (ver Parto::getStatusAttribute()).
     */
    public const STATUS_PARTO_CALCULADOS = ['Previsto', 'Próximo', 'Atrasado'];

    /** Gravados de verdade, porque são fatos que não dependem do relógio. */
    public const STATUS_PARTO_MANUAIS = ['Realizado', 'Cancelado', 'Perdido'];

    public const STATUS_LACTACAO = ['Ativa', 'Encerrada'];

    public const STATUS_BEZERRO = ['Vivo', 'Morto'];

    public const TURNOS_ORDENHA = ['Manhã', 'Tarde', 'Noite'];

    /** Dentro de quantos dias um parto previsto vira "Próximo". */
    public const DIAS_PROXIMO_PARTO = 15;

    /**
     * Status calculado de um parto ainda não realizado, a partir da data
     * prevista: sem data prevista continua "Previsto"; data já vencida (e
     * sem parto real registrado) vira "Atrasado"; dentro da janela de
     * DIAS_PROXIMO_PARTO vira "Próximo"; do contrário, "Previsto".
     */
    public static function statusPartoPrevisto(?string $dataPrevista): string
    {
        if (!$dataPrevista) {
            return 'Previsto';
        }

        $prevista = Carbon::parse($dataPrevista)->startOfDay();
        $hoje = Carbon::today();

        if ($prevista->lt($hoje)) {
            return 'Atrasado';
        }

        return $hoje->diffInDays($prevista) <= self::DIAS_PROXIMO_PARTO ? 'Próximo' : 'Previsto';
    }

    /**
     * Classe do badge de status (reaproveita o mesmo status-badge/status-ok
     * /status-baixo/status-esgotado já usado em Estoque/Cocho, em vez de
     * criar uma paleta nova): verde = Realizado/Ativa/Em lactação, amarelo
     * = Próximo, vermelho = Atrasado, cinza = qualquer outro estado neutro
     * (Previsto, Cancelado, Perdido, Encerrada, Vazia, Período seco...).
     */
    public static function corBadge(string $status): string
    {
        return match ($status) {
            'Realizado', 'Ativa', 'Ativo', 'Em lactação', 'Vivo' => 'status-ok',
            'Próximo', 'Prenhe' => 'status-baixo',
            'Atrasado' => 'status-esgotado',
            default => 'status-indefinido',
        };
    }
}
