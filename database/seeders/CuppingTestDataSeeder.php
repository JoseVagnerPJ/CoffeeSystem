<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CuppingSession;
use App\Models\CuppingItem;
use App\Models\Cup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CuppingTestDataSeeder extends Seeder
{
    public function run()
    {
        // Pega o primeiro usuário ou cria um se não existir
        $user = User::first() ?? User::factory()->create();

        // Vamos gerar dados para os últimos 60 dias para um histórico robusto
        for ($i = 60; $i >= 0; $i--) {
            $dataBase = Carbon::now()->subDays($i);
            
            // Lógica de "Sazonalidade": alguns dias são muito agitados, outros parados
            // Geramos de 0 a 5 mesas por dia
            $quantidadeMesasNoDia = rand(0, 5);

            for ($j = 1; $j <= $quantidadeMesasNoDia; $j++) {
                // Horários variados durante o dia (entre 08h e 20h)
                $horarioInicio = $dataBase->copy()->addHours(rand(8, 19))->addMinutes(rand(0, 59));
                
                // Algumas mesas ainda estão "abertas", a maioria "finalizada"
                $status = (rand(1, 10) > 8) ? 'aberto' : 'finalizado';
                $finishedAt = ($status == 'finalizado') ? $horarioInicio->copy()->addMinutes(rand(20, 120)) : null;

                // 1. Criar a Mesa
                $session = CuppingSession::create([
                    'title' => "Mesa Prova #" . rand(100, 999) . " - " . ($j > 1 ? "Lote B" : "Lote A"),
                    'session_date' => $dataBase->toDateString(),
                    'started_at' => $horarioInicio,
                    'finished_at' => $finishedAt,
                    'created_by' => $user->id,
                    'status' => $status,
                    'notes' => 'Simulação de rotina laboratorial intensa.'
                ]);

                // 2. Participante (Join na mesa)
                DB::table('cupping_session_user')->insert([
                    'cupping_session_id' => $session->id,
                    'user_id' => $user->id,
                    'has_finished' => ($status == 'finalizado'),
                    'created_at' => $horarioInicio,
                    'updated_at' => $horarioInicio
                ]);

                // 3. Quantidade de Xícaras (Variando de 3 até 12 para criar picos no gráfico)
                $qtdXicaras = rand(3, 12);
                
                for ($p = 1; $p <= $qtdXicaras; $p++) {
                    $item = CuppingItem::create([
                        'cupping_session_id' => $session->id,
                        'position' => $p,
                        'nickname' => "Amostra " . chr(64 + $p), // Amostra A, B, C...
                        'is_avulso' => true
                    ]);

                    // 4. Se a mesa estiver finalizada, gera notas aleatórias (SCA)
                    if ($status == 'finalizado') {
                        Cup::create([
                            'cupping_item_id' => $item->id,
                            'user_id' => $user->id,
                            'acidity' => rand(70, 95) / 10,
                            'body' => rand(70, 95) / 10,
                            'sweetness' => rand(80, 100) / 10,
                            'aftertaste' => rand(70, 90) / 10,
                        ]);
                    }
                }
            }
        }
    }
}