<?php

# Display errors for testing purposes
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

// This class is needed for upload
require_once('Upload.php');
// Trigger the upload method
// There are two optional parameters
// 1. $dir = 'upload/'
// - sets the file upload directory name
// 2. $validFormats = array("jpg", "png", "gif", "zip", "bmp")
// - Sets the array of allowed formats
$upload = Upload::upload('github/PHP-File-Multiupload/upload');
print_r($upload);
