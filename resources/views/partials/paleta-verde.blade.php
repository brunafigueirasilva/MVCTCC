{{-- ==========================================================================
     PALETA VERDE OFICIAL DO AGRIFARM — a mesma do app Flutter
     (projeto_tcc/lib/theme/app_colors.dart). Todo verde de botão, filtro,
     aba, link, seletor, estado ativo ou ícone de destaque usa estas
     variáveis; nenhuma página declara um tom de verde próprio.

       --verde-principal   botões, filtros/abas selecionados (preenchimento)
       --verde-texto       links, ícones de destaque, textos verdes
       --verde-secundario  hover de filtros, destaques vivos
       --verde-claro       fundos suaves (chips, ícones, linhas ativas)
       --verde-hover       hover de botões (variação do principal)
       --verde-ativo       pressionado / foco
       --verde-borda       borda discreta (filtros no estado normal)
       --verde-desabilitado

     No tema escuro o principal (#0E4A27) some sobre o fundo: os
     preenchimentos passam ao "ativo" (#087A47, texto branco com contraste
     AA) e textos/ícones ao secundário (#16A765).

     Incluído pelo layouts/app e pelas telas de login/cadastro. As páginas
     usam var(--verde-..., #fallback), então funcionam mesmo sem ele.
     ========================================================================== --}}
<style>
    :root {
        --verde-principal: #0e4a27;
        --verde-secundario: #16a765;
        --verde-claro: #e8f5ee;
        --verde-hover: #0b3d21;
        --verde-ativo: #087a47;
        --verde-texto: #0e4a27;
        --verde-borda: rgba(14, 74, 39, 0.28);
        --verde-desabilitado: rgba(14, 74, 39, 0.38);
    }

    body.dark-theme {
        --verde-principal: #087a47;
        --verde-secundario: #16a765;
        --verde-claro: rgba(22, 167, 101, 0.14);
        --verde-hover: #16a765;
        --verde-ativo: #16a765;
        /* Texto verde no escuro um tom acima do secundário: #16a765 sobre os
           fundos esverdeados dos badges/cards dava 4,3:1 (abaixo do AA);
           #1fb872 dá 5,2:1 a 6,3:1 sem sair da paleta. */
        --verde-texto: #1fb872;
        --verde-borda: rgba(22, 167, 101, 0.35);
        --verde-desabilitado: rgba(22, 167, 101, 0.35);
    }

    /* ---------- Filtros de período: 7 dias | 15 dias | 30 dias | 60 dias | 90 dias ----------
       Normal: fundo branco + borda verde discreta.
       Hover: verde secundário + texto branco.
       Selecionado: verde principal + texto branco. */
    .periodo-chips,
    .presets-periodo {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .periodo-chip,
    .btn-preset {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 999px;
        background: var(--card-bg, #ffffff);
        border: 1px solid var(--verde-borda);
        color: var(--text-principal, #0f172a);
        font-family: inherit;
        font-size: 0.8125rem;
        font-weight: 600;
        line-height: 1.2;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 0.16s ease, border-color 0.16s ease, color 0.16s ease;
    }

    .periodo-chip:hover,
    .btn-preset:hover {
        background: var(--verde-secundario);
        border-color: var(--verde-secundario);
        color: #ffffff;
    }

    .periodo-chip.active,
    .btn-preset.ativo {
        background: var(--verde-principal);
        border-color: var(--verde-principal);
        color: #ffffff;
        font-weight: 700;
    }

    .periodo-chip:focus-visible,
    .btn-preset:focus-visible {
        outline: 2px solid var(--verde-ativo);
        outline-offset: 2px;
    }

    .periodo-chip:disabled,
    .btn-preset:disabled {
        background: var(--card-bg, #ffffff);
        border-color: var(--verde-desabilitado);
        color: var(--verde-desabilitado);
        cursor: not-allowed;
    }

    body.high-contrast .periodo-chip,
    body.high-contrast .btn-preset {
        background: #000000 !important;
        border: 2px solid #ffff00 !important;
        color: #ffff00 !important;
    }

    body.high-contrast .periodo-chip:hover,
    body.high-contrast .periodo-chip.active,
    body.high-contrast .btn-preset:hover,
    body.high-contrast .btn-preset.ativo {
        background: #ffff00 !important;
        color: #000000 !important;
    }
</style>
