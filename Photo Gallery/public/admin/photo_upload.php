<?php
require_once('../../includes/functions.php');
require_once('../../includes/session.php');
require_once('../../includes/User.php');
require_once('../../includes/photograph.php');
if(!$session->is_logged_in()){redirect_to("login.php");}
?>
<?php
$message = "";
if(isset($_POST['submit']))
{
    $photo = new Photograph();
    $photo->caption = $_POST['caption'];
    $photo->attach_file($_FILES['file_upload']);
    if($photo->save())
    {
        $message = "Photograph uploaded successfully";
    } else {
        $message = join("<br />", $photo->errors);
    }

}

?>
<html>
<head>
<title>Photo Gallery</title>
<link href="../stylesheets/styles.css" media="all" rel="stylesheet" type="text/css" />
</head>
<body>
<h2>Photo Upload</h2>

<?php echo $message; ?>
<form action="photo_upload.php" enctype="multipart/form-data" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
<p><input type="file" name="file_upload" /></p>
<p>Caption: <input type="text" name="caption" value="" /></p>
<input type="submit" name="submit" value="Upload" />
</form>









<div id="footer">Copyright <?php echo date("Y", time()); ?>
</body>
</html>