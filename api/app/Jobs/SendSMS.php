<?php

namespace App\Jobs;

use App\Services\SMS;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSMS implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public $mobile,
        public $text
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        SMS::send($this->mobile, $this->text);
    }
}
