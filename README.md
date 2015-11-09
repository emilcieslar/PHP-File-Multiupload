# PHP File Multiupload
PHP Class that facilitates multiple file upload

## GETTING STARTED
1. Copy Upload.php to you project directory
2. `require_once('Upload.php');` in the file that handles the upload
3. Initialize the class and provide the `$_FILES['yourfilesname']` array `$upload = new Upload($_FILES['files']);`
4. Set upload directory or root directory if necessary (default directory is 'upload')
`$upload->setDir('github/PHP-File-Multiupload/upload');`
5. Upload your files using: `$upload->upload();`;
6. And that's it!**

## MANUAL
#### Upload.php
