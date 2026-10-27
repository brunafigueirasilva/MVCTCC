<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Garante que toda rota "interna" do sistema (dashboard, fazendas, lotes,
 * rações, cochos, plano alimentar, ordenha/leite, relatórios, perfil) exige
 * sessão válida — e que, sem sessão, a resposta é sempre a correta para o
 * tipo de consumidor (redirect para /login numa navegação normal, 401 em
 * JSON para os endpoints AJAX). Estes testes não dependem do schema
 * completo do banco (cadastros/fazendas/lotes) porque a própria middleware
 * (EnsureCadastroAutenticado) responde ANTES de qualquer Controller/consulta
 * rodar quando não há usuário na sessão.
 */
class ProtecaoDeRotasTest extends TestCase
{
    // A página inicial (pública) consulta os lotes — com as migrations das
    // tabelas-base o banco de teste agora pode ser montado normalmente.
    use RefreshDatabase;

    /** @return array<string, array{0: string, 1: string}> */
    public static function rotasProtegidasNavegacao(): array
    {
        return [
            'dashboard' => ['GET', '/dashboard'],
            'perfil' => ['GET', '/perfil'],
            'fazenda.listar' => ['GET', '/fazenda/listar'],
            'racao.listar' => ['GET', '/racao/listar'],
            'estoque.listar' => ['GET', '/estoque/listar'],
            'plano.listar' => ['GET', '/plano/listar'],
            'lote.listar' => ['GET', '/lotes'],
            'cocho.listar' => ['GET', '/cocho/listar'],
            'ordenha.listar' => ['GET', '/ordenha-leite'],
            'relatorio.listar' => ['GET', '/relatorios'],
            'relatorio.pdf' => ['GET', '/relatorio-pdf'],
        ];
    }

    #[DataProvider('rotasProtegidasNavegacao')]
    public function test_rota_protegida_redireciona_visitante_para_login(string $metodo, string $uri): void
    {
        $resposta = $this->call($metodo, $uri);

        $resposta->assertRedirect(route('login.index'));
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function rotasProtegidasJson(): array
    {
        return [
            'plano.lotesConsumoJson' => ['GET', '/plano/lotes-consumo'],
            'plano.conversoesJson' => ['GET', '/plano/conversoes-alimentares'],
            'relatorio.rankingJson' => ['GET', '/relatorios/ranking'],
            'ordenha.historicoJson' => ['GET', '/ordenha-leite/historico'],
            'lote.ordenha.historico' => ['GET', '/lote/1/ordenha/historico'],
            'assistenteIa.perguntar' => ['POST', '/assistente-ia/perguntar'],
        ];
    }

    /**
     * Antes da middleware central, alguns destes endpoints (histórico de
     * ordenha, ranking do relatório, assistente de IA) não tinham NENHUMA
     * checagem de sessão — respondiam normalmente para visitante anônimo.
     */
    #[DataProvider('rotasProtegidasJson')]
    public function test_endpoint_json_protegido_devolve_401_para_visitante(string $metodo, string $uri): void
    {
        $resposta = $this->call($metodo, $uri, [], [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $resposta->assertStatus(401);
        $resposta->assertJsonStructure(['message']);
    }

    public function test_pagina_de_login_continua_publica(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_pagina_inicial_continua_publica(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_rotas_de_administracao_de_cadastro_por_id_foram_removidas(): void
    {
        // /cadastro/{id} (editar/atualizar/excluir) permitia que QUALQUER
        // visitante alterasse ou excluísse a conta de QUALQUER usuário só
        // trocando o id na URL — sem tela nenhuma apontando para elas. Como
        // não há mais rota registrada com esse nome, buscar a URL deve
        // falhar já na geração da rota.
        $this->expectException(\Symfony\Component\Routing\Exception\RouteNotFoundException::class);

        route('cadastro.edit', ['id' => 1]);
    }
}
