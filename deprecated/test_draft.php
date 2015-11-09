<?php

// This class is needed for upload
require_once('Upload.php');
// Create the instance and pass in the files array
$upload = new Upload($_FILES['files']);
// Set the upload directory
$upload->setDir('github/PHP-File-Multiupload/upload');

// Upload the files
$upload->upload();
