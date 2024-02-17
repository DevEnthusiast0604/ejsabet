<?php

namespace App\Console\Commands;

use App\Models\Image;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use League\Flysystem\MountManager;

class TransactionImageUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:upload-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload images from storage folder to s3';

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
        ini_set('max_execution_time', 0);

        // Upload images from hostinger to s3
        $this->uploadImages();

        // Download images from contabo to hostinger
        $this->downloadImages();
        return Command::SUCCESS;
    }

    public function fileExists($path = null, $disk = 'public') {
        return Storage::disk($disk)->exists($path);
    }

    public function uploadImages() {
        $mountManager = new MountManager([
            'public' => Storage::disk('public')->getDriver(),
            's3' => Storage::disk('s3')->getDriver(),
        ]);
        $images = Image::whereNull('copied')->orderBy('id')->get();
        foreach ($images as $item) {
            $original_path = ltrim($item->path, '/');
            if ($this->fileExists($original_path, 'public') && !$this->fileExists($original_path, 's3')) {
                $mountManager->copy("public://$original_path", "s3://$original_path");
                $item->update(['copied' => 1]);
            } else {
                $item->update(['copied' => 1]);
            }
        }
    }

    public function downloadImages() {
        $mountManager = new MountManager([
            'contabo' => Storage::disk('contabo')->getDriver(),
            'public' => Storage::disk('public')->getDriver(),
        ]);
        $images = Image::whereNull('copied')->get();
        foreach ($images as $item) {
            $original_path = ltrim($item->path, '/');
            if ($this->fileExists($original_path, 'contabo') && !$this->fileExists($original_path, 'public')) {
                $mountManager->copy("contabo://$original_path", "public://$original_path");
                $item->update(['copied' => 1]);
            } else {
                $item->update(['copied' => 1]);
            }
        }

    }
}
