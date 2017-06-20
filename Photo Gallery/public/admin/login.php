<?php
require_once("../../includes/functions.php");
require_once("../../includes/session.php");
require_once("../../includes/Database.php");
require_once("../../includes/User.php");

if($session->is_logged_in())
{
    redirect_to("index.php");
}

//remember to give your form's submit tag a name="submit" attribute
if(isset($_POST['submit']))
{
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

//check database to see if user exists
$found_user = User::authenticate($username, $password);
    if($found_user)
    {
        $session->login($found_user);
        redirect_to("index.php");
    } else{
        $message = "The username/password combination was incorrect";
    }
} else{
    $username = "";
    $password = "";
}
?>
<html>
    <head>
    <title>Photo Gallery</title>
    <link href="../../stylesheets/styles.css" media="all" type="text/css" />
    </head>
    <body>
        <div id="header">
            <h1>Photo Gallery</h1>
        </div>
        <div id="main">
            <h2>Staff Login</h2>
            <?php echo output_message($message);?>
            <form action="login.php" method="post">
            <table>
                <tr>
                    <td>Username:</td>
                    <td>
                        <input type="text" name="username" maxlength="30" value="<?php 
                   echo htmlentities($username);?> "/>
                    </td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td>
                        <input type="text" name="password" maxlength="30" value="<?php 
                        echo htmlentities($password);?>"/>
                    </td>
                </tr>
                <td colspan="2">
                <input type="submit" name="submit" value="Login" />
                </td>
            </table>
        </div>
    </body>
</html>
<?php if(isset($database)){$database->close_connection();} ?>