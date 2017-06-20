<?php
require_once('../../includes/functions.php');
require_once('../../includes/session.php');
require_once('../../includes/User.php');
if(!$session->is_logged_in()){redirect_to("login.php");}
?>
<!doctype html>
<html lang="en">
<head>
<title>Photo Gallery</title>
<link href="../stylesheets/styles.css" media="all" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="header">
<h1>Photo Gallery</h1>
</div>
<div id="main">
<h2>Menu</h2>
<?php 
   // $user = new User();
    //$user->username = "John316";
   // $user->password = "banana4";
   // $user->first_name = "John";
   // $user->last_name = "Smith";
   // $user->create();

  
?>
</div>
<div id="footer">Copyright <?php echo date("Y", time()); ?>
</body>
</html>