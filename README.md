# PHP File Multiupload
PHP Class that facilitates multiple file upload

## GETTING STARTED
1. Copy Upload.php to you project directory
2. `require_once('Upload.php');` in the file that handles the upload
3. Upload your files using: `Upload::upload()`;
4. And that's it!**

## MANUAL
#### Upload.php
Method upload() has 4 optional parameters

1. **@param String $dir the directory where files should be uploaded**
Default for $dir = 'upload'
2. **@param String[] $validFormats the array containing allowed formats**
Default for $validFormats = array("jpg", "png", "gif", "zip", "bmp")
3. **@param int $maxFileSize maximum file size in MBs**
Default for $maxFileSize = 2;
4. **@param String $root root to the upload directory**
Default for $root = '', which means the root will be set automatically using global variable $_SERVER['DOCUMENT_ROOT']
