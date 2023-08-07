<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Settings;
use Illuminate\Support\Str;

class GenerateDefaultSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-default-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //Kezdeti beállítások létrehozása
        $this->info("Access token generálása...");
        $hash = hash('sha256', $plainTextToken = Str::random(40)); //Legeneráljunk a token-t
        Settings::create(['setting_name'=>'access_token', 'setting_value'=>$hash]);
        $this->warn("Az új access token értéke: ".$hash);
        $this->warn("FIGYELEM: Ez csak most látható!");
        $this->info("isEntryEnabled beállítása...");
        Settings::create(['setting_name'=>'isEntryEnabled', 'setting_value'=>true]);
        $this->info("isExitEnabled beállítása...");
        Settings::create(['setting_name'=>'isExitEnabled', 'setting_value'=>true]);
        $this->info("setup_cardId beállítása...");
        Settings::create(['setting_name'=>'setup_cardId', 'setting_value'=>'']);
        $this->info("Kész!"); //Kiírjuk a felhasználónak, ha sikeresen végeztünk a művelettel
    }
}
