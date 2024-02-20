<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DeleteImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $disk;
    protected $path;

    public function __construct($disk, $path)
    {
        $this->disk = $disk;
        $this->path = $path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Storage::disk($this->disk)->delete($this->path);
    }
}
