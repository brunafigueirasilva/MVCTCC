<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->string('finalidade')->nullable()->after('tipo_animal');
            $table->string('sexo')->nullable()->after('finalidade');
            $table->string('idade_media')->nullable()->after('sexo');
        });
    }

    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->dropColumn(['finalidade', 'sexo', 'idade_media']);
        });
    }
};
