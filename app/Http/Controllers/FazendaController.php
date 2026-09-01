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

    private function exigirLogin()
    {
        if (!session()->has('usuario_id')) {
            return redirect()->route('login.index');
        }

        return null;
    }

    // LISTAR
    public function listar()
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $fazendas = $this->fazendasDoUsuario()->orderBy('nome_fazenda')->get();
        $cadastroLogado = Cadastro::find(session('usuario_id'));

        return view('cadastrofazenda', compact('fazendas', 'cadastroLogado'));
    }

    // TELA CADASTRAR
    public function cadastrar()
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $fazendas = $this->fazendasDoUsuario()->orderBy('nome_fazenda')->get();
        $cadastroLogado = Cadastro::find(session('usuario_id'));

        return view('cadastrofazenda', compact('fazendas', 'cadastroLogado'));
    }

    // ADICIONAR
    public function add(Request $request)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

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

        Fazenda::create([
            // O dono é sempre o usuário da sessão atual — nunca um valor
            // vindo do formulário, que poderia ser manipulado para
            // cadastrar a fazenda em nome de outra pessoa. O nome do
            // proprietário exibido também vem sempre da sessão, pelo mesmo
            // motivo.
            'cadastro_id' => session('usuario_id'),
            'proprietario' => session('usuario_nome'),
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
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $this->fazendasDoUsuario()->findOrFail($id);

        return redirect()->route('fazenda.listar');
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

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
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $fazenda = $this->fazendasDoUsuario()->findOrFail($id);

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
