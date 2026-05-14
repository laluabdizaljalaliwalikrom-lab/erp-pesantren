<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Exception;

class TestGcsConnection extends Command
{
    protected $signature = 'test:gcs';
    protected $description = 'Test Google Cloud Storage connection and permissions';

    public function handle()
    {
        $this->info('Starting GCS Connection Test...');
        
        $disk = 'gcs';
        $filename = 'test-connection-' . now()->timestamp . '.txt';
        $content = 'GCS connection test successful at ' . now()->toDateTimeString();

        $config = config("filesystems.disks.{$disk}");
        $this->table(['Setting', 'Value'], [
            ['Driver', $config['driver'] ?? 'N/A'],
            ['Project ID', $config['project_id'] ?? 'N/A'],
            ['Bucket', $config['bucket'] ?? 'N/A'],
            ['Key File Path', $config['key_file_path'] ?? 'Using ADC'],
        ]);

        try {
            $this->comment("1. Attempting to write file: {$filename}...");
            $result = Storage::disk($disk)->put($filename, $content);
            
            if ($result) {
                $this->info('   [SUCCESS] File written successfully.');
            } else {
                $this->error('   [FAILURE] Storage::put returned false without exception.');
            }

            $this->comment('2. Attempting to check file existence...');
            // Clear cache just in case
            clearstatcache();
            
            if (Storage::disk($disk)->exists($filename)) {
                $this->info('   [SUCCESS] File exists on GCS.');
            } else {
                $this->error('   [FAILURE] File was written but could not be found.');
                
                $this->comment('Checking all files in bucket...');
                $files = Storage::disk($disk)->files();
                if (empty($files)) {
                    $this->line('   Bucket appears to be empty.');
                } else {
                    $this->line('   Files found in bucket: ' . implode(', ', $files));
                }
                return;
            }

            $this->comment('3. Attempting to get file URL...');
            $url = Storage::disk($disk)->url($filename);
            $this->info("   [INFO] Public URL: {$url}");

            $this->comment('4. Attempting to delete test file...');
            Storage::disk($disk)->delete($filename);
            $this->info('   [SUCCESS] Test file deleted successfully.');

            $this->info('=========================================');
            $this->info('GCS CONNECTION TEST COMPLETED SUCCESSFULLY');
            $this->info('=========================================');

        } catch (Exception $e) {
            $this->error('=========================================');
            $this->error('GCS CONNECTION TEST FAILED');
            $this->error('=========================================');
            $this->error('Error Message: ' . $e->getMessage());
            $this->line($e->getTraceAsString());
        }
    }
}
