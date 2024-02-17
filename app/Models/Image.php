<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    protected $guarded = [];

    protected $appends = ['src'];

    public function imageable() {
        return $this->morphTo();
    }

    public function getSrcAttribute() {
        if ($this->disk === 'contabo') {
            return config('filesystems.disks.contabo.url') . $this->path;
        } else if ($this->disk === 's3') {
            $path = ltrim($this->path, '/');
            return Storage::disk('s3')->url($path);
        } else {
            return url('storage' . $this->path);
        }
    }
}
