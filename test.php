<?php

// This class is needed for upload
require_once('Upload.php');
// Create the instance and pass in the files array
$upload = new Upload($_FILES['files']);
// Set the upload directory
$upload->setDir('github/PHP-File-Multiupload/upload');

// Upload the files
$upload->upload();

// Get error messages if any
if($errors = $upload->getErrors())
  print_r($errors);

// Get names of uploaded files
if($files = $upload->getUploadedFilesNames())
  print_r($files);
