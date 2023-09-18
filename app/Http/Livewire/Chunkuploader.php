<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;

class Chunkuploader extends Component
{
    use WithFileUploads;

    public int $chunkSize = 1000000; // 2Mb #todo - заменить на актуальный размер
    public $fileChunk;

    private string $tempFolder = '/livewire-tmp/';
    private string $finalFolder = '/public/video/';

    public string $fileName;
    public string $fileSize;
    public $finalFile;

    public string $inputId;
    public int $progressPercentage;

    public function updatedFileChunk()
    {
        $chunkFileName = $this->fileChunk->getFileName();
        $finalPath     = Storage::path('/livewire-tmp/' . $this->fileName);
        $tmpPath       = Storage::path('/livewire-tmp/' . $chunkFileName);

        $tempfile = fopen($tmpPath, 'rb');
        $final = fopen($finalPath, 'ab');

        stream_copy_to_stream($tempfile, $final);
        fclose($tempfile);
        fclose($final);
        unlink($tmpPath);

        $curSize = Storage::size($this->tempFolder . $this->fileName);
        if ($curSize != $this->fileSize) {
            return;
        }

        $this->finalFile = TemporaryUploadedFile::createFromLivewire('/' . $this->fileName);
        $path            = $this->tempFolder . $this->fileName;
        $newPath         = $this->finalFolder . $this->fileName;
        Storage::move($path, $newPath);

        $this->garbageCollector();
    }

    public function render()
    {
        return view('livewire.chunked-file-upload');
    }

    private function garbageCollector(): void
    {
        $files = Storage::files($this->tempFolder);
        if (empty($files)) {
            return;
        }

        foreach ($files as $file) {
            if (Storage::exists($file) === false) {
                continue;
            }

            $creationTime = Storage::lastModified($file);
            $currentTime  = time();
            if (($currentTime - $creationTime) < 3600) {
                continue;
            }

            Storage::delete($file);
        }
    }
}