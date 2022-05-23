<?php

namespace App\Jobs;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessRequestLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $ip;

    private string $method;

    private string $uri;

    private string $visitAt;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $ip, string $method, string $uri, string $visitAt)
    {
        $this->ip = $ip;
        $this->method = $method;
        $this->uri = $uri;
        $this->visitAt = $visitAt;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Request::query()->create([
            'ip' => DB::raw("INET6_ATON('$this->ip')"),
            'method' => $this->method,
            'uri' => $this->uri,
            'visit_at' => $this->visitAt
        ]);
    }
}
