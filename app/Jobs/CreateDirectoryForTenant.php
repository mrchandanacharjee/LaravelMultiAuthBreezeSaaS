<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class CreateDirectoryForTenant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $tenant;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->tenant->run(function ($tenant) {
            
            $storage_path = storage_path();

            mkdir("$storage_path/framework/cache", 0777, true);
            mkdir("$storage_path/app/public", 0777, true);
        });
    }
}
