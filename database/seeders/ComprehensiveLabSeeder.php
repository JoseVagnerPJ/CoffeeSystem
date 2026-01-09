<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CuppingSession;
use App\Models\CuppingItem;
use App\Models\Cup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ComprehensiveLabSeeder extends Seeder
{
    public function run()
    {
        // Limpeza para evitar duplicidade excessiva nos testes
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        CuppingSession::truncate();
        CuppingItem::truncate();
        Cup::truncate();
        DB::table('cupping_session_user')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $user = User::first() ?? User::factory()->create();
        $now = Carbon::now('America/Sao_Paulo');

        // --- 1. DADOS PARA AS ÚLTIMAS 24 HORAS (Filtro 'D') ---
        // Criamos mesas em horários específicos para ver a oscilação por hora
        $hoursWithActivity = [2, 5, 8, 9, 10, 14, 15, 17, 20]; 
        foreach ($hoursWithActivity as $h) {
            $date = $now->copy()->subHours($h);
            $this->createMesa($date, $user, "Mesa Fluxo 24h - " . $date->format('H:i'));
        }

        // --- 2. DADOS PARA OS ÚLTIMOS 30 DIAS (Filtros 'S' e 'M') ---
        for ($i = 1; $i <= 30; $i++) {
            $date = $now->copy()->subDays($i);
            
            // Ignora alguns dias para o gráfico ter "vales" (zeros) realistas
            if (in_array($date->dayOfWeek, [0, 6]) && rand(0, 1)) continue; 

            // Quantidade de mesas varia por dia (0 a 4)
            $mesasNoDia = rand(1, 4);
            for ($j = 1; $j <= $mesasNoDia; $j++) {
                $this->createMesa($date->copy()->setTime(rand(8, 18), 0), $user, "Mesa Histórica D-".$i);
            }
        }
    }

    private function createMesa($dateTime, $user, $title)
    {
        $session = CuppingSession::create([
            'title' => $title,
            'session_date' => $dateTime->toDateString(),
            'started_at' => $dateTime,
            'finished_at' => $dateTime->copy()->addMinutes(rand(30, 60)),
            'created_by' => $user->id,
            'status' => 'finalizado',
            'created_at' => $dateTime, // Importante para a Query do Controller
        ]);

        DB::table('cupping_session_user')->insert([
            'cupping_session_id' => $session->id,
            'user_id' => $user->id,
            'has_finished' => true,
            'created_at' => $dateTime,
        ]);

        // Criamos entre 4 e 12 xícaras por mesa
        $qtdCups = rand(4, 12);
        for ($k = 1; $k <= $qtdCups; $k++) {
            $item = CuppingItem::create([
                'cupping_session_id' => $session->id,
                'position' => $k,
                'nickname' => "Am. " . chr(64 + $k),
                'is_avulso' => true,
                'created_at' => $dateTime,
            ]);

            Cup::create([
                'cupping_item_id' => $item->id,
                'user_id' => $user->id,
                'acidity' => rand(70, 85) / 10,
                'body' => rand(70, 85) / 10,
                'sweetness' => rand(75, 95) / 10,
                'aftertaste' => rand(70, 80) / 10,
                'created_at' => $dateTime,
            ]);
        }
    }
}