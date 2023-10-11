<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemSideOperations;

class CheckOperationTimeout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-operation-timeout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rendszerszintű műveletek esetén történő időtúllépést figyelő parancs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $operations = SystemSideOperations::where('operation_state', '!=', 'successful')->where('operation_state', '!=', 'failed')->where('operation_state', '!=', 'timeout')->where('operation_state', '!=', 'created')->get(); //Lekérdezzük azokat a műveleteket, amelyek nem lettek készen, de nem is léptek ki az adott odőkeretből
        $time = time(); //Lekérdezzük az aktuális időt
        foreach ($operations as $current){ //Végigiterálunk a műveleteken
            if($time - strtotime($current->sent_time) > $current->timeout){ //Ha jelenlegi idő és az elküldési idő különbsége nagyobb, mint a maximálisan meegengedhető időtúllépés
                $current->operation_state = 'timeout'; //Akkor a művelet állapota legyen "timeout"
                $current->save(); //Mentsük a változásokat
            }
        }
    }
}
