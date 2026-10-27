<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * php artisan db:seed — dados de demonstração do AgriFarm (usuários,
     * fazendas, lotes, cochos, rações, estoque, planos, ordenhas...).
     * O sistema usa a tabela "cadastros" (não a "users" padrão do Laravel).
     */
    public function run(): void
    {
        $this->call(AgriFarmDemoSeeder::class);
    }
}
