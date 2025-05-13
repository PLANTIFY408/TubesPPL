<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Land;
use App\Models\SensorData;
use Carbon\Carbon;

class GenerateDummySensorData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sensor:generate-dummy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate data sensor dummy untuk semua lahan aktif';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lands = Land::active()->get();
        foreach ($lands as $land) {
            SensorData::create([
                'land_id' => $land->id,
                'ph_value' => rand(55, 75) / 10, // 5.5 - 7.5
                'moisture_value' => rand(600, 900) / 10, // 60 - 90
                'temperature' => rand(250, 350) / 10, // 25 - 35
                'humidity' => rand(500, 900) / 10, // 50 - 90
                'timestamp' => Carbon::now(),
            ]);
        }
        $this->info('Data sensor dummy berhasil ditambahkan.');
    }
}
