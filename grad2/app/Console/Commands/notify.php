<?php

namespace App\Console\Commands;

use App\Mail\notifyEmail;
use App\Models\ShopOwner;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class notify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send mail to users before plan ends';

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
     * @return int
     */
    public function handle()
    {
        $emails=ShopOwner::where('is_active',1)->where('expires_at',Carbon::now()->addDay()->toDateString())->Pluck('email')->toArray();
        foreach($emails as $email)
        {
            Mail::To($email)->send(new notifyEmail());
        }
    }
}
