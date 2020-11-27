<?php

namespace App\Jobs;

use App\Mail\ShareLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ShareLinkQuizlet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $from_address = '';
    public $to_address = '';
    public $share_link = '';
    public function __construct($from=null, $to=null, $link=null)
    {
        $this->from_address = $from;
        $this->to_address = $to;
        $this->share_link = $link;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->to_address)->queue(new ShareLink($this->from_address, $this->to_address, $this->share_link));
    }
}
