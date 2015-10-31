<?php

public class Upload
{
  public function upload()
  {
    $valid_formats = array("jpg", "png", "gif", "zip", "bmp");
      $max_file_size = 1024*1000*2; //2000 kb
      $path = $_SERVER['DOCUMENT_ROOT'] . substr(SITE_URL,1) . "upload/"; // Upload directory
      $count = 0;

      # Get if it's komiks being uploaded
      $komiks = (isset($_POST['type']) && $_POST['type'] == "1") ? true : false;

      # Loop $_FILES to exeicute all files
    	foreach ($_FILES['files']['name'] as $f => $name) {

    	    if ($_FILES['files']['error'][$f] == 4) {
    	        continue; # Skip file if any error found
    	    }

    	    if ($_FILES['files']['error'][$f] == 0) {

    	        if ($_FILES['files']['size'][$f] > $max_file_size)
              {
  	            $message[] = "$name is too large!.";
  	            continue; # Skip large files

    	        } elseif( ! in_array(pathinfo($name, PATHINFO_EXTENSION), $valid_formats) )
              {
        				$message[] = "$name is not a valid format";
        				continue; # Skip invalid file formats

        			} else
              { # No error found! Move uploaded files
                # Create unique name
                $uniqueName = md5(uniqid(rand(), true));
                $uniqueName .= "." . pathinfo($name, PATHINFO_EXTENSION);

                # Get album ID
                $albumId = $_POST['album'];

                # Move uploaded file
  	            if(move_uploaded_file($_FILES["files"]["tmp_name"][$f], $path.$uniqueName))
  	            $count++; # Number of successfully uploaded file

                # Resize image
                $resizedImg = new Imagick($path.$uniqueName);
                $resizedImg->cropThumbnailImage(180,180);
                $resizedImg->writeImage($path."th_".$uniqueName);
                $resizedImg->destroy();

                # Save image
                $image = $this->model('Image');
                $image->setName($name);
                $image->setFilename($uniqueName);
                # If it's komiks image
                if($komiks)
                  $image->setType(1);
                $image->setAlbumId($albumId);
                $image->Save();
    	        }
    	    }
    	}
  }
}
