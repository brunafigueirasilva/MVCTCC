<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\LoteRegras;

/** Endpoints de apoio nativos da API. */
class ApoioController extends Controller
{
    // GET /api/opcoes — listas fixas usadas nos formulários (tipos, finalidades, doenças...)
    public function opcoes()
    {
        return response()->json(['data' => [
            'tiposAnimais' => LoteRegras::tiposAnimais(),
            'finalidadesPorAnimal' => LoteRegras::FINALIDADES_POR_ANIMAL,
            'sexos' => LoteRegras::SEXOS,
            'idadesMedias' => LoteRegras::IDADES_MEDIAS,
            'statusLote' => LoteRegras::STATUS,
            'faixaPesoPorAnimal' => LoteRegras::FAIXA_PESO_POR_ANIMAL,
            'doencasPorAnimal' => LoteRegras::DOENCAS_POR_ANIMAL,
            'doencaOutra' => LoteRegras::DOENCA_OUTRA,
            'causasMorte' => LoteRegras::CAUSAS_MORTE,
            'causaMorteOutra' => LoteRegras::CAUSA_MORTE_OUTRA,
            'tiposComOrdenha' => LoteRegras::tiposComOrdenha(),
            'tiposComParto' => LoteRegras::tiposComParto(),
            'tiposComPostura' => LoteRegras::tiposComPostura(),
            'tiposComEclosao' => LoteRegras::tiposComEclosao(),
            'turnosOrdenha' => LoteRegras::TURNOS_ORDENHA,
        ]]);
    }
}
