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

  // Holds the singleton instance
  private static $instance = null;


  // Constant for MegaBytes to Bytes conversion
  const MEGA = 1024*1024;


  /**
   * Private constructor that creates the instance only once during the execution
   */
  private function __construct($files)
  {
    // Set array of files that will be uploaded
    $this->files = $files;

    /** Set defaults for instance variables **/
    // Empty array for filesSuccess and errors
    $this->filesSuccess = [];
    $this->errors = [];
    // Default max file size 2 MBs
    $this->maxFileSize = 2*Upload::MEGA;
    // Default formats are images
    $this->allowedFormats = array("jpg", "png", "gif", "bmp");
    // Default final directory is 'upload'
    $this->dir = 'upload';
    // Default root directory is DOCUMENT_ROOT
    $this->root = $_SERVER['DOCUMENT_ROOT'];
    // Set the whole path for the upload directory
    $this->path = $this->root . '/' . $this->dir . '/';
  }

  /**
   * Function init
   *
   * Initializes the singleton instance
   *
   * @param Array $files the $_FILES['filesname'] array
   * @return the singleton instance of Upload class
   */
  public static function init($files = null)
  {
    // If user did not specified files, exit
    if(!isset($files))
      return;

    // If the Upload instance doesn't exist yet, created it
    if(!isset(static::$instance))
        static::$instance = new Upload($files);

    // Return the instance in order for user to work with it, for example:
    // - upload files
    // - retrieve uploaded files or error messages
    // - change root or upload directory, etc.
    return static::$instance;
  }


  /**
   * Function upload
   *
   * Uploads all files from $files array
   */
  public static function upload($files = null)
  {
    // Loop $_FILES to go over all files
    foreach (static::$instance->files['name'] as $f => $name)
    {
      // Check for any errors during the upload
      if (static::$instance->files['error'][$f] != 0)
      {
        static::$instance->errors[] = static::getErrorString(static::$instance->files['error'][$f]);
        continue; // Skip file if any error found
      }

      // If no errors are found during the upload, continue
      if (static::$instance->files['error'][$f] == 0)
      {
        // Check if file is not too large
        if (static::$instance->files['size'][$f] > $maxFileSize)
        {
          static::$instance->errors[] = "$name is too large";
          continue; // Skip large files
        }
        // Check if it's allowed file format
        elseif( ! in_array(pathinfo($name, PATHINFO_EXTENSION), static::$instance->allowedFormats) )
        {
          static::$instance->errors[] = "$name is not a valid format";
          continue; // Skip invalid file formats
        }
        // No error found! Move uploaded files
        else
        {
          // Create unique name
          $uniqueName = static::generateUniqueName($name);

          // Move the uploaded file to its final destination
          if(move_uploaded_file(static::$instance->files["tmp_name"][$f], $path.$uniqueName))
            // Add the file to the array of successfully uploaded files
            static::$instance->filesSuccess[] = $uniqueName;
        }

      } // No errors during upload
    } // Foreach
  }


  /**
   * Set the root directory
   */
  public static function setRoot($root)
  {
    static::$instance->root = $root;
    static::$instance->setPath();
  }


  /**
   * Set the upload directory
   */
  public static function setDir($dir)
  {
    static::$instance->dir = $dir;
    static::$instance->setPath();
  }


  /**
   * Set the final path to upload directory
   */
  private function setPath()
  {
    $this->path = $this->root . '/' . $this->dir . '/';
  }


  private static function getErrorString($error)
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

  private static function generateUniqueName($name)
  {
    $uniqueName = md5(uniqid(rand(), true));
    // Add file extension to the name
    $uniqueName .= "." . pathinfo($name, PATHINFO_EXTENSION);
    return $uniqueName;
  }

}
