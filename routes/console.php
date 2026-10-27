<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Garante que o histórico de consumo (Plano Alimentar/Conversão Alimentar)
// não fique com buracos em dias sem ninguém abrir a tela.
Schedule::command('plano:capturar-snapshots')->dailyAt('23:55');
