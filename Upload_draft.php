<?php

/**
 * Upload class
 * A class to upload multiple files at once using one form input

 * @author Emil Cieslar
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
   * Private constructor that creates the upload instance
   *
   * @param Array $files the $_FILES['filesname'] array
   */
  public function __construct($files)
  {
    // Set array of files that will be uploaded
    $this->files = $files;

    /** Set defaults for instance variables **/
    // Empty array for filesSuccess and errors
    $this->filesSuccess = [];
    $this->errors = [];
    // Default max file size 2 MBs
    $this->maxFileSize = $this->convertFromMB(2);
    // Default formats are images
    $this->allowedFormats = array("jpg", "png", "gif", "bmp");
    // Default final directory is 'upload'
    $this->dir = 'upload';
    // Default root directory is DOCUMENT_ROOT
    $this->root = $_SERVER['DOCUMENT_ROOT'];
    // Set the whole path for the upload directory
    $this->path = $this->setPath();
  }


  /**
   * Function upload
   *
   * Uploads all files from $files array
   */
  public static function upload($files = null)
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
        if ($this->files['size'][$f] > $maxFileSize)
        {
          $this->errors[] = "$name is too large";
          continue; // Skip large files
        }
        // Check if it's allowed file format
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
          if(move_uploaded_file($this->files["tmp_name"][$f], $path.$uniqueName))
            // Add the file to the array of successfully uploaded files
            $this->filesSuccess[] = $uniqueName;
        }

      } // No errors during upload
    } // Foreach
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
    $this->path = $this->root . $this->dir . '/';
  }

  public function getPath()
  {
    return $this->path;
  }


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

  private function generateUniqueName($name)
  {
    $uniqueName = md5(uniqid(rand(), true));
    // Add file extension to the name
    $uniqueName .= "." . pathinfo($name, PATHINFO_EXTENSION);
    return $uniqueName;
  }


  /**
   * A method to convert MBs to Bytes
   */
  private function convertFromMB($MBs)
  {
    return $MBs*Upload::KILO*Upload::KILO;
  }

}
