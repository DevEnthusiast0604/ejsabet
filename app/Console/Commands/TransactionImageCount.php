<?php

namespace App\Console\Commands;

use App\Models\Image;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TransactionImageCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:count-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get count of images in contabo s3 bucket';

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

        $s3_files = Storage::disk('s3')->allFiles('transactions');
        $s3CountFiles = 0;
        if ($s3_files !== false) {
            $s3CountFiles = count($s3_files);
        }
        dump('S3: ', $s3CountFiles);

        $public_files = Storage::disk('public')->allFiles('transactions');
        $publicCountFiles = 0;
        if ($public_files !== false) {
            $publicCountFiles = count($public_files);
        }
        dump('Public: ', $publicCountFiles);

        // foreach ($files as $file) {
        //     $filename = basename($file);
        //     if (Image::where('path', 'like', "%$filename")->exists() === false) {
        //         dump($filename);
        //         // Storage::disk('public')->delete($file);
        //     }
        // }
        return Command::SUCCESS;
    }
}
