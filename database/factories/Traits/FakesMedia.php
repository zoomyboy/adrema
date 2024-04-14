<?php

namespace Database\Factories\Traits;

use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;

trait FakesMedia
{

    public function withImage(string $collection, string $filename): self
    {
        return $this->afterCreating(function (HasMedia $model) use ($filename, $collection) {
            $pathinfo = pathinfo($filename);

            UploadedFile::fake()->image($filename, 1000, 1000)->storeAs('media-library', $filename, 'temp');

            $model->addMediaFromDisk('media-library/' . $filename, 'temp')
                ->usingName($pathinfo['filename'])
                ->usingFileName($pathinfo['basename'])
                ->toMediaCollection($collection);
        });
    }

    public function withDocument(string $collection, string $filename, string $content = ''): self
    {
        return $this->afterCreating(function (HasMedia $model) use ($filename, $collection, $content) {
            $pathinfo = pathinfo($filename);

            UploadedFile::fake()->create($filename, $content, 'application/pdf')->storeAs('media-library', $filename, 'temp');

            $model->addMediaFromDisk('media-library/' . $filename, 'temp')
                ->usingName($pathinfo['filename'])
                ->usingFileName($pathinfo['basename'])
                ->toMediaCollection($collection);
        });
    }
}
