<?php

namespace Tests\Feature;

use App\Models\Cadastro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as UsuarioSocialite;
use Mockery;
use Tests\TestCase;

/**
 * Foto de perfil: "foto_url" pronto para exibir (foto enviada ou da conta
 * Google) e a foto do Google aplicada no login sem sobrescrever uma foto
 * enviada pelo próprio usuário.
 */
class FotoPerfilTest extends TestCase
{
    use RefreshDatabase;

    private const FOTO_GOOGLE = 'https://lh3.googleusercontent.com/a/foto-teste=s96-c';

    private function conta(array $extra = []): Cadastro
    {
        return Cadastro::create($extra + [
            'responsavel' => 'Bruna Teste',
            'email' => 'bruna.teste@example.com',
            'senha' => Hash::make('Senha@123'),
            'telefone' => '11999999999',
        ]);
    }

    private function fingirGoogle(string $email, ?string $avatar): void
    {
        $usuarioGoogle = (new UsuarioSocialite())->map([
            'id' => '123',
            'name' => 'Bruna Teste',
            'email' => $email,
            'avatar' => $avatar,
        ]);

        $provedor = Mockery::mock();
        $provedor->shouldReceive('stateless')->andReturnSelf();
        $provedor->shouldReceive('userFromToken')->andReturn($usuarioGoogle);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provedor);
    }

    public function test_sem_foto_foto_url_e_nulo(): void
    {
        $this->assertNull($this->conta()->foto_url);
    }

    public function test_foto_enviada_vira_url_do_storage(): void
    {
        $conta = $this->conta(['foto' => 'perfil/abc.jpg']);
        $this->assertSame(asset('storage/perfil/abc.jpg'), $conta->foto_url);
    }

    public function test_foto_do_google_e_usada_como_esta(): void
    {
        $conta = $this->conta(['foto' => self::FOTO_GOOGLE]);
        $this->assertSame(self::FOTO_GOOGLE, $conta->foto_url);
    }

    public function test_login_google_pela_api_grava_e_devolve_a_foto(): void
    {
        $this->fingirGoogle('novo.google@example.com', self::FOTO_GOOGLE);

        $resposta = $this->postJson('/api/auth/google', ['access_token' => 'token-falso'])->assertOk();

        $this->assertSame(self::FOTO_GOOGLE, data_get($resposta->json(), 'data.usuario.foto_url') ?? data_get($resposta->json(), 'usuario.foto_url'));
        $this->assertSame(self::FOTO_GOOGLE, Cadastro::where('email', 'novo.google@example.com')->value('foto'));
    }

    public function test_login_google_nao_troca_foto_enviada_pelo_usuario(): void
    {
        $this->conta(['foto' => 'perfil/minha.jpg']);
        $this->fingirGoogle('bruna.teste@example.com', self::FOTO_GOOGLE);

        $this->postJson('/api/auth/google', ['access_token' => 'token-falso'])->assertOk();

        $this->assertSame('perfil/minha.jpg', Cadastro::where('email', 'bruna.teste@example.com')->value('foto'));
    }

    public function test_login_google_sem_foto_mantem_avatar_padrao(): void
    {
        $this->fingirGoogle('sem.foto@example.com', null);

        $this->postJson('/api/auth/google', ['access_token' => 'token-falso'])->assertOk();

        $this->assertNull(Cadastro::where('email', 'sem.foto@example.com')->first()->foto_url);
    }
}
