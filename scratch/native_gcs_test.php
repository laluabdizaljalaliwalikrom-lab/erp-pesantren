<?php

require 'vendor/autoload.php';

use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Facade;

// Minimal Laravel-like environment for the test
$projectId = 'project-abe744c2-29af-4c9b-ab9';
$bucketName = 'erp-pesantren';

echo "Testing native GCS client...\n";
echo "Project: $projectId\n";
echo "Bucket: $bucketName\n";

try {
    $storage = new StorageClient([
        'projectId' => $projectId,
    ]);

    $bucket = $storage->bucket($bucketName);
    
    echo "Attempting to upload a small string...\n";
    $result = $bucket->upload('Native test content', [
        'name' => 'native-test.txt'
    ]);

    echo "Success! Object created: " . $result->name() . "\n";
    
    echo "Attempting to delete it...\n";
    $result->delete();
    echo "Deleted successfully.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    if (strpos($e->getMessage(), 'Could not find') !== false) {
        echo "Tip: Make sure the bucket name is exactly correct and exists in this project.\n";
    }
}
