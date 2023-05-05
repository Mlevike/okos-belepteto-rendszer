<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateFirstUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-first-user {name=admin} {email=admin@admin.com} {password=jelszo} {picture?} {code?} {fingerprint?} {language=hu} {profile=Admin} {cardId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A kiinduló admin felhasználó létrehozása.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        User::create(['name'=>$this->argument('name'), 'email'=>$this->argument('email'), 'email_verified_at'=> now(), 'password'=> Hash::make($this->argument('password'), ['memory' => 1024, 'time' => 2, 'threads' => 2,]), 'picture'=>$this->argument('picture') != null ? $this->argument('picture') : '', 'code'=> $this->argument('code') != null ? Hash::make($this->argument('code'), ['memory' => 1024, 'time' => 2, 'threads' => 2,]) : '' , 'fingerprint'=> $this->argument('fingerprint') != null ? $this->argument('fingerprint') : '', 'language'=>$this->argument('language'), 'profile'=>$this->argument('profile'), 'isAdmin'=> true, 'isWebEnabled'=> true, 'isEntryEnabled'=> true, 'isEmployee'=> true, 'cardId' => $this->argument('cardId') != null ? $this->argument('cardId'): '', 'isHere'=>false]); //A felhasználó létrehozása
        $this->info("Kész!");
    }
}
