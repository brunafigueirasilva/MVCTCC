<?php

namespace App\Http\Requests\Api;

use App\Models\Cocho;
use App\Models\Sensor;
use App\Services\CochoIotService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Leitura enviada pelo controlador IoT (POST /api/iot/leitura):
 *   {"cocho_id": 1, "sensor_id": "BAL-001", "tipo_sensor": "balanca", "peso_kg": 63.4}
 *   {"cocho_id": 1, "sensor_id": "DIST-001", "tipo_sensor": "distancia", "distancia_cm": 18.5}
 *   {"cocho_id": 1, "sensor_id": "BAL-001", "peso_kg": 4.25, "presenca_animal": true, "distancia_presenca_cm": 37.2}
 * "presenca_animal"/"distancia_presenca_cm" são opcionais: vêm do HC-SR04
 * usado como detector de animal na frente do cocho (firmware em iot/).
 * "sensor_id" é o CÓDIGO do sensor cadastrado no cocho; "tipo_sensor" é
 * opcional (vem do cadastro do sensor) e, se enviado, precisa bater.
 * "data_hora" é opcional (padrão: horário do servidor).
 *
 * Erro de valor (peso negativo, distância fora da faixa, peso maior que o
 * cocho) num sensor identificado marca o sensor com status "erro".
 */
class LeituraIotRequest extends FormRequest
{
    public ?Cocho $cocho = null;
    public ?Sensor $sensor = null;

    public function authorize(): bool
    {
        // A autenticação do dispositivo é feita pelo middleware iot.dispositivo.
        return true;
    }

    public function rules(): array
    {
        $diasAtraso = (int) config('iot.dias_maximos_leitura_atrasada', 30);

        return [
            'cocho_id' => ['required', 'integer', 'min:1'],
            'sensor_id' => ['required', 'string', 'max:50'],
            'tipo_sensor' => ['nullable', 'in:' . implode(',', Sensor::TIPOS)],
            'distancia_cm' => ['nullable', 'numeric', 'min:0', 'max:' . config('iot.distancia_maxima_cm', 500)],
            'peso_kg' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            // Ultrassônico usado como detector de animal no cocho (opcional).
            'presenca_animal' => ['nullable', 'boolean'],
            'distancia_presenca_cm' => ['nullable', 'numeric', 'min:0', 'max:' . config('iot.distancia_maxima_cm', 500)],
            'data_hora' => ['nullable', 'date', 'before_or_equal:' . now()->addMinutes(5)->toDateTimeString(), 'after:' . now()->subDays($diasAtraso)->toDateTimeString()],
        ];
    }

    public function messages(): array
    {
        return [
            'cocho_id.required' => 'Informe o cocho (cocho_id).',
            'sensor_id.required' => 'Informe o código do sensor (sensor_id).',
            'tipo_sensor.in' => 'tipo_sensor deve ser "distancia" ou "balanca".',
            'distancia_cm.min' => 'A distância não pode ser negativa.',
            'distancia_cm.max' => 'Distância fora da faixa aceita pelo sensor.',
            'peso_kg.min' => 'O peso não pode ser negativo.',
            'presenca_animal.boolean' => 'presenca_animal deve ser true ou false.',
            'distancia_presenca_cm.min' => 'A distância de presença não pode ser negativa.',
            'distancia_presenca_cm.max' => 'Distância de presença fora da faixa aceita pelo sensor.',
            'data_hora.before_or_equal' => 'data_hora não pode estar no futuro.',
            'data_hora.after' => 'Leitura antiga demais para ser registrada.',
        ];
    }

    public function after(): array
    {
        return [function (\Illuminate\Validation\Validator $validator) {
            if ($validator->errors()->hasAny(['cocho_id', 'sensor_id'])) {
                $this->resolverSensor();
                return;
            }

            $this->cocho = Cocho::find($this->integer('cocho_id'));
            if (!$this->cocho) {
                $validator->errors()->add('cocho_id', 'Cocho não encontrado.');
                return;
            }

            $this->resolverSensor();
            if (!$this->sensor) {
                $validator->errors()->add('sensor_id', 'Sensor não cadastrado. Cadastre o código do sensor no cocho antes de enviar leituras.');
                return;
            }

            if ((int) $this->sensor->cocho_id !== (int) $this->cocho->id) {
                $validator->errors()->add('sensor_id', 'Este sensor não pertence ao cocho informado.');
                $this->sensor = null; // não marca erro num sensor de outro cocho
                return;
            }

            if (!$this->sensor->ativo) {
                $validator->errors()->add('sensor_id', 'Sensor desativado neste cocho.');
                return;
            }

            if ($this->filled('tipo_sensor') && $this->input('tipo_sensor') !== $this->sensor->tipo) {
                $validator->errors()->add('tipo_sensor', 'tipo_sensor não corresponde ao tipo cadastrado para este sensor (' . $this->sensor->tipo . ').');
                return;
            }

            if ($this->sensor->tipo === Sensor::TIPO_BALANCA) {
                if (!$this->has('peso_kg') || $this->input('peso_kg') === null) {
                    $validator->errors()->add('peso_kg', 'Leitura de balança sem peso_kg.');
                    return;
                }

                $capacidade = (float) $this->cocho->capacidade;
                $tolerancia = (float) config('iot.tolerancia_peso_percentual', 10);
                if ($capacidade > 0 && !$validator->errors()->has('peso_kg') && (float) $this->input('peso_kg') > $capacidade * (1 + $tolerancia / 100)) {
                    $validator->errors()->add('peso_kg', 'Peso incompatível com o cocho: ' . $this->input('peso_kg') . ' kg excede a capacidade de ' . $capacidade . ' kg.');
                }
            } elseif (!$this->has('distancia_cm') || $this->input('distancia_cm') === null) {
                $validator->errors()->add('distancia_cm', 'Leitura de distância sem distancia_cm.');
            }
        }];
    }

    protected function failedValidation(Validator $validator): void
    {
        // Valor inválido vindo de um sensor identificado deste cocho: o
        // sensor está enviando dado ruim → status "erro" no painel.
        $erroDeValor = $validator->errors()->first('peso_kg') ?: $validator->errors()->first('distancia_cm');
        if ($this->sensor && $erroDeValor) {
            app(CochoIotService::class)->registrarErro($this->sensor, $erroDeValor);
        }

        parent::failedValidation($validator);
    }

    private function resolverSensor(): void
    {
        if ($this->sensor === null && is_string($this->input('sensor_id')) && $this->input('sensor_id') !== '') {
            $this->sensor = Sensor::where('codigo', trim($this->input('sensor_id')))->first();

            if ($this->sensor && $this->cocho === null && is_numeric($this->input('cocho_id'))) {
                $this->cocho = Cocho::find((int) $this->input('cocho_id'));
            }

            if ($this->sensor && $this->cocho && (int) $this->sensor->cocho_id !== (int) $this->cocho->id) {
                $this->sensor = null;
            }
        }
    }
}
