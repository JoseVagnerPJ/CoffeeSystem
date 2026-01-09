<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CuppingSession;
use App\Models\CuppingItem;
use App\Models\Cup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FullLaboratorySeeder extends Seeder
{
    public function run()
    {
        // Limpa as tabelas antes de começar (Opcional, mas recomendado para testes limpos)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        CuppingSession::truncate();
        CuppingItem::truncate();
        Cup::truncate();
        DB::table('cupping_session_user')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $user = User::first() ?? User::factory()->create();

        // Gera dados para os últimos 30 dias
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // Cria uma variação realista: fim de semana tem menos movimento
            $isWeekend = $date->isWeekend();
            $maxSessions = $isWeekend ? rand(0, 2) : rand(2, 6);
            $sessionsCount = rand(0, $maxSessions);

            for ($j = 0; $j < $sessionsCount; $j++) {
                // Distribui os horários entre 08:00 e 18:00
                $startAt = $date->copy()->setTime(rand(8, 18), rand(0, 59), 0);
                $status = rand(0, 10) > 8 ? 'aberto' : 'finalizado';
                $finishedAt = $status == 'finalizado' ? $startAt->copy()->addMinutes(rand(45, 120)) : null;

                $session = CuppingSession::create([
                    'title' => "Rotina Lab - Lote " . rand(100, 999),
                    'session_date' => $startAt->toDateString(),
                    'started_at' => $startAt,
                    'finished_at' => $finishedAt,
                    'created_by' => $user->id,
                    'status' => $status,
                    'notes' => 'Gerado automaticamente para teste de volume.'
                ]);

                 // Vincula usuário
                 DB::table('cupping_session_user')->insert([
                    'cupping_session_id' => $session->id,
                    'user_id' => $user->id,
                    'has_finished' => $status == 'finalizado',
                    'created_at' => $startAt,
                    'updated_at' => $startAt
                ]);

                // Varia as xícaras por mesa (entre 5 e 15) para distanciar as linhas no gráfico
                $cupsCount = rand(5, 15);
                for ($k = 1; $k <= $cupsCount; $k++) {
                    $item = CuppingItem::create([
                        'cupping_session_id' => $session->id,
                        'position' => $k,
                        'nickname' => "Am. " . $k,
                        'is_avulso' => true,
                        'created_at' => $startAt
                    ]);

                    if ($status == 'finalizado') {
                        Cup::create([
                            'cupping_item_id' => $item->id,
                            'user_id' => $user->id,
                            'acidity' => rand(7, 9), 'body' => rand(7, 9), 'sweetness' => rand(7, 10), 'aftertaste' => rand(7, 9),
                            'created_at' => $startAt
                        ]);
                    }
                }
            }
        }
    }
}