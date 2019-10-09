<?php
# Includes the autoloader for libraries installed with composer
require __DIR__ . "/vendor/autoload.php";

# Imports the Google Cloud client library
use Google\Cloud\Storage\StorageClient;

# Your Google Cloud Platform project ID
$projectId = 'super-data-fyp';

# Instantiates a client
$storage = new StorageClient([
    'projectId' => $projectId,
    'keyFilePath' => './config/super-data-12f9a5e1853f.json'
]);

# The name for bucket
$bucketName = 'super-data-fyp.appspot.com';
?>