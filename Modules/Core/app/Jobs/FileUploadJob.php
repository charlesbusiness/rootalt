<?php

namespace Modules\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Models\Upload;
use Modules\Core\Services\CoreService;

class FileUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        foreach ($this->data['files'] as $fileData) {
            $tempPath = $fileData['temp_path']; // Path in storage/app/temp

            if (Storage::exists($tempPath)) {
                // Upload file to S3
                $fileContents = Storage::get($tempPath);
                $fileName = "uploads/{$fileData['folder']}/" . uniqid() . '.' . pathinfo($tempPath, PATHINFO_EXTENSION);

                $res = Storage::disk('s3')->put($fileName, $fileContents);
                info("Response from aws========", [$res]);
                if ($res) {
                    // Save upload details to database
                    Upload::create([
                        'upload_owner' => $this->data['upload_owner'],
                        'entity_id' => $this->data['entity_id'],
                        'file_size' => $fileData['size'] . "kb",
                        'file_name' => $fileData['fileName'],
                        'upload_type' => $this->data['upload_type'],
                        'upload_path' => Storage::disk('s3')->url($fileName)
                    ]);
                }

                // Delete temporary file after upload
                Storage::delete($tempPath);
            }
        }
    }
}
