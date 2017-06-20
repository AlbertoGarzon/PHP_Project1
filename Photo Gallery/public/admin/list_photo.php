<?php
require_once('../../includes/functions.php');
require_once('../../includes/session.php');
require_once('../../includes/User.php');
require_once('../../includes/photograph.php');
if(!$session->is_logged_in()){redirect_to("login.php");}
?>
<?php
//Find all photos
$photos = Photograph::find_all();
?>
<html>
<head>
<title>Photo Gallery</title>
<link href="../stylesheets/styles.css" media="all" rel="stylesheet" type="text/css" />
</head>
<body>
<h2>View Photos from Database</h2>
<table class="bordered">
    <tr>
        <th>Image</th>
        <th>Filename</th>
        <th>Caption</th>
        <th>Size</th>
        <th>Type</th>
    </tr>
    <?php foreach($photos as $photo); ?>
    <tr>
        <td><img src="" width="100" /></td>
        <td><?php echo $photo->filename; ?></td>
        <td><?php echo $photo->caption; ?></td>
        <td><?php echo $photo->size; ?></td>
        <td><?php echo $photo->type; ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<br />
<div id="footer">Copyright <?php echo date("Y", time()); ?>
</body>
</html>