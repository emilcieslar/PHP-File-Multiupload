<?php

# Display errors for testing purposes
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

// This class is needed for upload
require_once('Upload_draft.php');
$upload = new Upload(array());
print_r($upload->getPath());

// Trigger the upload method
//$upload = Upload::upload('github/PHP-File-Multiupload/upload');
// Print the results
//print_r($upload);
