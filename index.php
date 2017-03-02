<?php

ini_set("display_error", 1);
error_reporting(E_ALL);

function getExtension($str){
	$i = strrpos($str,".");
	if (!$i) { return ""; }

	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return $ext;
}

$valid_formats = array("jpg", "png", "gif", "bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");

$msg='';
$class="";
if($_SERVER['REQUEST_METHOD'] == "POST")
{

	$name = $_FILES['file']['name'];
	$size = $_FILES['file']['size'];
	$tmp = $_FILES['file']['tmp_name'];
	$ext = getExtension($name);

	if(strlen($name) > 0){

		if(in_array($ext,$valid_formats)){

			if($size<(2024*2024)){
				include('s3_config.php');
				//Rename image name.
				$actual_image_name = time().".".$ext;
				if($s3->putObjectFile($tmp, $bucket , $actual_image_name, S3::ACL_PUBLIC_READ) ){
					$msg = "S3 Upload Successful.";
					$class=" alert-success alert-dismissible fade in";

					$s3file='http://'.$bucket.'.s3.amazonaws.com/'.$actual_image_name;
					echo "<img src='$s3file' style='max-width:400px'/><br/>";
					echo '<b><a href="'.$s3file.'">S3 File URL</a></b>';
				}
				else{
					$msg = "S3 Upload Fail.";
					$class=" alert-danger alert-dismissible fade in";
				}

			}
			else{
				$msg = "Image size Max 2 MB";
				$class=" alert-danger alert-dismissible fade in";
			}

		}
		else {
			$msg = "Invalid file, please upload image file.";
			$class=" alert-danger alert-dismissible fade in";
		}
	}
	else {
		$msg = "Please select image file.";
		$class=" alert-danger alert-dismissible fade in";
	}
}

?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Upload Files to Amazon S3 PHP</title>
<!-- Latest compiled and minified CSS & JS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<script src="//code.jquery.com/jquery.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</head>

<body>

  <main class="container">
    <form action="" method='post' enctype="multipart/form-data">
      <h3>Upload image file here</h3>
      <div style='margin:10px'>
        <input type='file' name='file'/> <br />
        <input type='submit' value='Upload Image'/>
      </div>
    </form>
    <?php if (!empty($msg)) {
    ?>
      <div class="alert col-md-3 <?php echo $class; ?>">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong><?php echo $msg; ?></strong>
      </div>
    <?php
    } ?>
  </main>

</body>
</html>
