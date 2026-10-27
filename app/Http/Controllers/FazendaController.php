<?php

namespace App\Http\Controllers;

use App\Models\Cadastro;
use App\Models\Fazenda;
use Illuminate\Http\Request;

class FazendaController extends Controller
{
    /** As 27 UFs reais do Brasil — o estado informado precisa ser uma delas. */
    private const UFS_VALIDAS = 'AC,AL,AP,AM,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RS,RO,RR,SC,SP,SE,TO';

    /**
     * Cada fazenda pertence a um único usuário (cadastro_id). Todo o
     * controller trabalha só em cima das fazendas do usuário logado —
     * nunca confiando em um cadastro_id vindo do cliente — para que os
     * dados de uma conta nunca apareçam, sejam editados ou excluídos por
     * outra.
     */
    private function fazendasDoUsuario()
    {
        return Fazenda::where('cadastro_id', session('usuario_id'));
    }

    // A autenticação em si (sessão válida) já é garantida pelo middleware
    // 'auth.cadastro' aplicado ao grupo de rotas (ver routes/web.php) —
    // este controller só cuida de escopar os dados ao usuário logado.

    // LISTAR
    public function listar()
    {
        return view('cadastrofazenda', $this->dadosListagem());
    }

    // TELA CADASTRAR
    public function cadastrar()
    {
        return view('cadastrofazenda', $this->dadosListagem());
    }

    /** Fazendas do usuário com os números do "portfólio" (lotes, animais,
     *  rações e cochos de cada uma) usados nos cards da tela. */
    private function dadosListagem(): array
    {
        $fazendas = $this->fazendasDoUsuario()
            ->withCount(['lotes', 'racoes', 'cochos'])
            ->withSum('lotes as total_animais', 'quantidade_animais')
            ->orderBy('nome_fazenda')
            ->get();
        $cadastroLogado = Cadastro::find(session('usuario_id'));

        return compact('fazendas', 'cadastroLogado');
    }

    // ADICIONAR
    public function add(Request $request)
    {
        $dados = $request->validate([
            'nome_fazenda' => 'required|string|max:255',
            'cep' => 'nullable|string|max:9',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|size:2|in:' . self::UFS_VALIDAS,
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'area' => 'required|numeric|gt:0',
        ], [
            'area.gt' => 'A área da fazenda deve ser maior que zero.',
        ]);

        // O dono é sempre o usuário autenticado: a fazenda é criada pela
        // relação Cadastro → fazendas(), que preenche cadastro_id sozinha.
        // cadastro_id não está no $fillable, então nem um campo
        // "cadastro_id" enviado no request consegue trocar o dono, e nunca
        // fica sem valor (erro 1364 "doesn't have a default value").
        $cadastro = Cadastro::findOrFail(session('usuario_id'));

        $cadastro->fazendas()->create([
            // O nome do proprietário exibido também vem sempre do cadastro
            // logado, pelo mesmo motivo.
            'proprietario' => $cadastro->responsavel,
            'nome_fazenda' => $dados['nome_fazenda'],
            'cep' => $dados['cep'] ?? null,
            'cidade' => $dados['cidade'] ?? null,
            'estado' => $dados['estado'] ?? null,
            'latitude' => $dados['latitude'] ?? null,
            'longitude' => $dados['longitude'] ?? null,
            // Mantido para compatibilidade com qualquer código antigo que
            // ainda leia "localizacao" como um texto único.
            'localizacao' => $this->localizacaoResumo($dados),
            'area' => $dados['area'],
        ]);

        return redirect()->route('fazenda.listar')
            ->with('success', 'Fazenda cadastrada com sucesso!');
    }

    // TELA ATUALIZAR
    // A edição acontece via modal na própria tela de listagem, então
    // apenas redirecionamos para lá em vez de renderizar uma página dedicada.
    public function atualizar($id)
    {
        $this->fazendasDoUsuario()->findOrFail($id);

        return redirect()->route('fazenda.listar');
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $dados = $request->validate([
            'nome_fazenda' => 'required|string|max:255',
            'cep' => 'nullable|string|max:9',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|size:2|in:' . self::UFS_VALIDAS,
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'area' => 'required|numeric|gt:0',
        ], [
            'area.gt' => 'A área da fazenda deve ser maior que zero.',
        ]);

        $fazenda = $this->fazendasDoUsuario()->findOrFail($id);

        $fazenda->update([
            'proprietario' => session('usuario_nome'),
            'nome_fazenda' => $dados['nome_fazenda'],
            'cep' => $dados['cep'] ?? null,
            'cidade' => $dados['cidade'] ?? null,
            'estado' => $dados['estado'] ?? null,
            'latitude' => $dados['latitude'] ?? null,
            'longitude' => $dados['longitude'] ?? null,
            'localizacao' => $this->localizacaoResumo($dados),
            'area' => $dados['area'],
        ]);

        return redirect()->route('fazenda.listar')
            ->with('success', 'Fazenda atualizada com sucesso!');
    }

    // DELETE
    public function deletar($id)
    {
        $fazenda = $this->fazendasDoUsuario()->findOrFail($id);

        // Excluir a fazenda apagaria em cascata lotes, cochos (com a ração
        // dentro), rações e todo o histórico de estoque, ordenha e consumo.
        // Só sai uma fazenda vazia; com dados, o produtor resolve antes.
        $lotes = $fazenda->lotes()->count();
        $racoes = $fazenda->racoes()->count();

        if ($lotes > 0 || $racoes > 0) {
            $partes = array_filter([
                $lotes ? "{$lotes} lote(s)" : null,
                $racoes ? "{$racoes} ração(ões)" : null,
            ]);

            return back()->withErrors([
                'fazenda' => "A fazenda \"{$fazenda->nome_fazenda}\" não pode ser excluída: ainda tem " . implode(' e ', $partes) . ' cadastrados. Exclua (ou encerre) esses registros antes — assim nenhum histórico é apagado sem querer.',
            ]);
        }

        $fazenda->delete();

        return redirect()->route('fazenda.listar')
            ->with('success', 'Fazenda excluída com sucesso!');
    }

    /** "Cidade - UF" a partir dos campos preenchidos pela consulta de CEP. */
    private function localizacaoResumo(array $dados): ?string
    {
        $partes = array_filter([$dados['cidade'] ?? null, $dados['estado'] ?? null]);

        return $partes ? implode(' - ', $partes) : null;
    }
}
