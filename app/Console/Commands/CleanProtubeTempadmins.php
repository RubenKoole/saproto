<?php

namespace Proto\Console\Commands;

use Carbon;
use Illuminate\Console\Command;
use Proto\Models\Tempadmin;

class CleanProtubeTempadmins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:cleanprotubeadmins';

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
     */
    public function handle()
    {
        Tempadmin::query()->where('end_at', '<=', Carbon::now())->delete();
    }
}
