<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CuppingSession;
use App\Models\CuppingItem;
use App\Models\Cup;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CuppingSessionSeeder extends Seeder
{
    public function run()
    {
        // 1. Garante que existam Tags para as avaliações
        $tagsData = ['Frutado', 'Chocolatado', 'Floral', 'Citrico', 'Encorpado', 'Caramelo', 'Nozes', 'Vinhoso'];
        foreach ($tagsData as $tagName) {
            Tag::firstOrCreate(['name' => $tagName]);
        }
        $tags = Tag::all();

        // 2. Busca o primeiro usuário do sistema para ser o criador
        $user = User::first() ?? User::factory()->create(['name' => 'José Vagner']);

        // 3. Define o período: 2 anos atrás até hoje
        $startDate = Carbon::now()->subYears(2);
        $endDate = Carbon::now();

        $this->command->info("Iniciando a geração de 2 anos de mesas de cupping...");

        DB::beginTransaction();

        try {
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                
                // Cria a Sessão Diária (main_company_id removido)
                $session = CuppingSession::create([
                    'title' => 'Mesa Diária - ' . $date->format('d/m/Y'),
                    'session_date' => $date->format('Y-m-d'),
                    'created_by' => $user->id,
                    'status' => 'finalizado',
                    'items_count' => rand(3, 6),
                    'notes' => 'Gerado automaticamente para teste de volume.',
                    'started_at' => $date->copy()->setTime(9, 0, 0),
                    'finished_at' => $date->copy()->setTime(11, 30, 0),
                ]);

                // Cria os itens da sessão (Todos Avulsos)
                for ($i = 1; $i <= $session->items_count; $i++) {
                    $item = CuppingItem::create([
                        'cupping_session_id' => $session->id,
                        'sample_id' => null, 
                        'position' => $i,
                        'is_avulso' => 1,
                        'nickname' => 'Lote ' . chr(rand(65, 90)) . rand(100, 999),
                        'variety' => 'Arábica - ' . (rand(0, 1) ? 'Catuaí' : 'Bourbon'),
                        'drying_process' => rand(0, 1) ? 'Natural' : 'Cereja Despolpado',
                        'fermentation' => rand(0, 1) ? 'Induzida' : 'Nenhuma',
                        'humidity' => rand(10, 12) . '.' . rand(0, 9),
                        'screen' => rand(16, 18),
                        'altitude' => rand(900, 1400),
                        'crop' => '24/25',
                        'notes' => 'Amostra avulsa de teste.'
                    ]);

                    // Cria a avaliação (Cup)
                    // O Model Cup calcula o final_score automaticamente no boot
                    $cup = Cup::create([
                        'cupping_item_id' => $item->id,
                        'user_id' => $user->id,
                        'acidity' => rand(700, 900) / 100,
                        'body' => rand(700, 900) / 100,
                        'sweetness' => rand(800, 1000) / 100,
                        'aftertaste' => rand(700, 850) / 100,
                        'is_invalid' => false,
                        'observations' => 'Avaliação automática gerada pelo seeder.'
                    ]);

                    // Vincula Tags aleatórias
                    $cup->tags()->attach($tags->random(2)->pluck('id'));
                }
            }

            DB::commit();
            $this->command->info("Sucesso! 730 sessões diárias criadas.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Erro ao rodar seeder: " . $e->getMessage());
        }
    }
}