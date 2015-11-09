<?php

/**
 * Upload class
 * A class to upload multiple files at once using one form input

 * @author Emil Cieslar
 * @website http://webkreativ.cz
 */
class Upload
{
  // Files array
  private $files;

  // Successfully uploaded files array
  private $filesSuccess;

  // Any errors during the upload process
  private $errors;

  // Maximum size of single file that can be uploaded
  private $maxFileSize;

  // Allowed formats for upload
  private $allowedFormats;

  // Final directory that the files will be uploaded
  private $dir;

  // Root directory
  private $root;

  // The whole path that file should be uploaded
  private $path;


  // Constant for MegaBytes to Bytes conversion
  const KILO = 1024;


  /**
   * Creates the upload instance and sets defaults for instance variables
   *
   * @param Array $files the $_FILES['filesname'] array
   */
  public function __construct($files = null)
  {
    // Set array of files that will be uploaded
    // If user did not provide the files array, set $_FILES['files'] as default
    if(!isset($files))
      $this->files = (isset($_FILES['files'])) ? $_FILES['files'] : array();
    else
      $this->files = $files;

    /** Set defaults for instance variables **/
    // FilesSuccess and errors are by default empty
    $this->filesSuccess = false;
    $this->errors = false;
    // Default max file size 2 MBs
    $this->maxFileSize = $this->convertFromMB(2);
    // Default formats are images
    $this->allowedFormats = array("jpg", "png", "gif", "bmp");
    // Default final directory is 'upload'
    $this->dir = 'upload';
    // Default root directory is DOCUMENT_ROOT
    $this->root = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;

    // Set the whole path for the upload directory
    $this->setPath();
  }


  /**
   * Function upload
   *
   * Uploads all files from $files array
   */
  public function upload()
  {
    // Loop $_FILES to go over all files
    foreach ($this->files['name'] as $f => $name)
    {
      // Check for any errors during the upload
      if ($this->files['error'][$f] != 0)
      {
        $this->errors[] = $this->getErrorString($this->files['error'][$f]);
        continue; // Skip file if any error found
      }

      // If no errors are found during the upload, continue
      if ($this->files['error'][$f] == 0)
      {
        // Check if file is not too large
        if ($this->files['size'][$f] > $this->maxFileSize)
        {
          $this->errors[] = "$name is too large";
          continue; // Skip large files
        }

        // Check if the file has allowed file format
        elseif( ! in_array(pathinfo($name, PATHINFO_EXTENSION), $this->allowedFormats) )
        {
          $this->errors[] = "$name is not a valid format";
          continue; // Skip invalid file formats
        }

        // No error found! Move uploaded files
        else
        {
          // Create unique name
          $uniqueName = $this->generateUniqueName($name);

          // Move the uploaded file to its final destination
          if(move_uploaded_file($this->files["tmp_name"][$f], $this->path . $uniqueName))
            // Add the file to the array of successfully uploaded files
            $this->filesSuccess[] = $uniqueName;
        }

      } // No errors during upload
    } // For each file
  }


  /**
   * Set the root directory
   */
  public function setRoot($root)
  {
    $this->root = $root;
    $this->setPath();
  }


  /**
   * Set the upload directory
   */
  public function setDir($dir)
  {
    $this->dir = $dir;
    $this->setPath();
  }


  /**
   * Set the final path to upload directory
   */
  private function setPath()
  {
    $this->path = $this->root . $this->dir . DIRECTORY_SEPARATOR;
  }


  /**
   * Return the full path where files will be uploaded
   *
   * @return String full path
   */
  public function getPath()
  {
    return $this->path;
  }


  /**
   * Return the array of names of successfully uploaded files
   *
   * @return String[] uploaded files' names
   */
  public function getUploadedFilesNames()
  {
    return $this->filesSuccess;
  }


  /**
   * Return the array of error messages
   *
   * @return String[] error messages
   */
  public function getErrors()
  {
    return $this->errors;      
  }


  /**
   * Set the allowed formats array, for example:
   * array("jpg", "png", "gif", "bmp")
   *
   * @param String[] $allowedFormats
   */
  public function setAllowedFormats($allowedFormats)
  {
    $this->allowedFormats = $allowedFormats;
  }


  /**
   * Function getErrorString
   *
   * @return String the string format of the integer error constant
   */
  private function getErrorString($error)
  {
    $phpFileUploadErrors = array(
        0 => 'There is no error, the file uploaded with success',
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload.',
    );

    return $phpFileUploadErrors[$error];
  }


  /**
   * Function generateUniqueName
   *
   * @return String the unique name of the file
   */
  private function generateUniqueName($name)
  {
    $uniqueName = md5(uniqid(rand(), true));
    // Add file extension to the name
    $uniqueName .= "." . pathinfo($name, PATHINFO_EXTENSION);
    return $uniqueName;
  }


  /**
   * Function convertFromMB
   *
   * @param $MBs int the MBs
   * @return int Bytes converted from MBs
   */
  private function convertFromMB($MBs)
  {
    return $MBs*Upload::KILO*Upload::KILO;
  }

}
