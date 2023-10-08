<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Planeacion\Pdd\Eje;

class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $new = new eje;
        $new->name = "copia";
        $new->pdd_id = 1;
        $new->save();
    }
}
