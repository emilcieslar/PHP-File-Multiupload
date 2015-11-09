<?php

/**
 * Upload class
 *
 * @author Emil Cieslar
 */
class Upload
{
  public function __construct() {
    self::upload();
  }

  /**
   * Function upload
   *
   * Uploads all files from $_FILES array
   * @param String $dir the directory where files should be uploaded
   * @param String[] $validFormats the array containing allowed formats
   * @param int $maxFileSize maximum file size in MBs
   * @param String $root root to the upload directory
   *
   * @return String[] contains 'error' and 'files' fields:
   *                  'error' are any error messages
   *                  'files' are successfully uploaded files
   */
  public static function upload($dir = 'upload',
                                $validFormats = array("jpg", "png", "gif", "zip", "bmp"),
                                $maxFileSize = 2,
                                $root = '')
  {
    // Set path for the upload directory
    if(empty($root))
      $path = $_SERVER['DOCUMENT_ROOT'] . '/' . $dir . '/';
    else
      $path = $root . '/' . $dir . '/';

    // Set maximum files size in MB
    $maxFileSize *= 1024*1000;

    // Array that will contain names of uploaded files
    $uploadedFiles = [];
    // Array that will contain error messages if any
    $message = [];

    // Loop $_FILES to go over all files
    foreach ($_FILES['files']['name'] as $f => $name)
    {
      // Check for any errors
      // You can read up more about errors when uploading files here: TODO: Add link to errors explained in PHP manual
      if ($_FILES['files']['error'][$f] != 0)
      {
        switch($_FILES['files']['error'][$f])
        {
          case UPLOAD_ERR_INI_SIZE:
            $message[] = "$name is too large!.";
            break;
          case UPLOAD_ERR_FORM_SIZE:
          case UPLOAD_ERR_PARTIAL:
          case UPLOAD_ERR_NO_FILE:
          case UPLOAD_ERR_CANT_WRITE:
            $message[] = "Cannot write to disk";
            break;
          default:
        }
        continue; // Skip file if any error found
      }

      // If no errors are found, continue
      if ($_FILES['files']['error'][$f] == 0)
      {
        // Check if file is not too large
        if ($_FILES['files']['size'][$f] > $maxFileSize)
        {
          $message[] = "$name is too large!.";
          continue; // Skip large files
        }
        // Check if it's allowed file format
        elseif( ! in_array(pathinfo($name, PATHINFO_EXTENSION), $validFormats) )
        {
          $message[] = "$name is not a valid format";
          continue; // Skip invalid file formats

        }
        // No error found! Move uploaded files
        else
        {
          // Create unique name
          $uniqueName = md5(uniqid(rand(), true));
          // Add file extension to the name
          $uniqueName .= "." . pathinfo($name, PATHINFO_EXTENSION);

          // Move the uploaded file to its final destination
          if(move_uploaded_file($_FILES["files"]["tmp_name"][$f], $path.$uniqueName))

          // Add the file to the array
          $uploadedFiles[] = $uniqueName;
        }

      } // No errors
    } // Foreach

    // return String[] contains 'error' and 'files' fields:
    // 1. 'error' are any error messages
    // 2. 'files' are successfully uploaded files
    return array('errors'=>$message,'files'=>$uploadedFiles);
  }

}
