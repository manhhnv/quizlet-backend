<?php

namespace App\Jobs;

use App\Mail\JoinClass;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendJoinRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $from_address = '';
    public $to_address = '';
    public $confirm_link = '';
    public function __construct($from=null, $to=null, $confirm=null)
    {
        $this->from_address = $from;
        $this->to_address = $to;
        $this->confirm_link = $confirm;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->to_address)->queue(new JoinClass($this->from_address, $this->to_address, $this->confirm_link));
    }
}
