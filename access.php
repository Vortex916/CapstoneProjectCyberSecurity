<!-- Authenticate a registered user. -->
<?php
include('config.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="<?php echo $design; ?>/style.css" rel="stylesheet" title="Style" />
        <title>Registered user access</title>
    </head>
    <body>
    	<div class="header">
        	<h1>Cybersecurity Capstone Project</h1>
	    </div>
<?php
//If the user is logged, we log him out
if(isset($_SESSION['username']))
{
	//We log him out by deleting the username and userid sessions
	unset($_SESSION['username'], $_SESSION['userid']);
?>
<div class="message">You have successfuly been logged out.<br />
<?php
}
else
{
	$ousername = '';
	//We check if the form has been sent
	if(isset($_POST['username'], $_POST['password']))
	{
		$username = mysqli_real_escape_string($link, $_POST['username']);
		$password = mysqli_real_escape_string($link, $_POST['password']);

		/* create a prepared statement */
		$stmt = $link->prepare("SELECT id FROM users WHERE username=?");

		/* bind parameters for markers */
		if ($stmt->bind_param("s", $username_test))
		{
			echo "bind_param successful<br />";
		}
		else
		{
			echo "bind_param not successful<br />";
		}
		$username_test = "Tester4";
		
		/* execute query */
		$stmt->execute();
		echo "execute successful<br />";
		$stmt->close();
		echo "close successful<br />";
		
		//We get the password of the user
		//echo '<script type="text/javascript">alert("prepare")</script>';
		//$stmt = $link->prepare("select password,id,salt from users where username=?"); // prepare sql statement for execution
		//echo '<script type="text/javascript">alert("bind")</script>';
		// if ($stmt == true)
		// {			
			// echo '<script type="text/javascript">alert("successful, bindparam now")</script>';
			// $result = $stmt->bindParam('s', $username); // bind variables to prepared statement as parameters
			// //$username_test = $username;
			// echo '<script type="text/javascript">alert("successful")</script>';
			// $stmt->execute();
			
		// }
		// else
		// {
			// echo '<script type="text/javascript">alert("link prepare not successful")</script>';
		// }
		//echo '<script type="text/javascript">alert("execute")</script>';
		//$stmt->execute(); // execute prepared statement
		//echo '<script type="text/javascript">alert("get result")</script>';
		//$req = $stmt->get_result();
		//echo '<script type="text/javascript">alert("fetch array")</script>';
		//$dn = $req->fetch_array();
		//echo '<script type="text/javascript">alert("close")</script>';
  		//$stmt->close();

		$req = mysqli_query($link, 'select password,id,salt from users where username="'.$username.'"');
		echo "query successful<br />";
		$dn  = mysqli_fetch_array($req);
		echo "fetch array successful<br />";
		$password = hash("sha512", $dn['salt'].$password); // Hash with the salt to match database.
		echo "password successful<br />";
		
		//We compare the submited password and the real one, and we check if the user exists
		if ($dn['password'] == $password and mysqli_num_rows($req)>0) 
		{
			//If the password is good, we dont show the form
			$form = false;
			//We save the user name in the session username and the user Id in the session userid
			$_SESSION['username'] = $_POST['username'];
			$_SESSION['userid'] = $dn['id'];
			// go to start page
			header('Location: index.php');
		}
		else 
		{
			//Otherwise, we say the password is incorrect.
			$form    = true;
			$message = 'The entered username is not registered or the password does not fit to the registered username. Click on link <a href="password_forgotten.php">Password forgotten?</a><br/> in case you forgot the password.';
		}
	}
	else $form = true;
	
	if($form) 
	{
		//We display a message if necessary
		if(isset($message)) echo '<div class="message">'.$message.'</div>';

	//We display the form
?>
<div class="content">
    <form action="access.php" method="post">
        Please type your username and password to log in:<br />
		<br />
        <div class="center">
            <label for="username">Username</label><input type="text" name="username" id="username" value="<?php echo htmlentities($ousername, ENT_QUOTES, 'UTF-8'); ?>" /><br />
            <label for="password">Password</label><input type="password" name="password" id="password" /><br /><br />
            <input type="submit" value="Login" />
		</div>
    </form>
	
	<br/><a href="password_forgotten.php">Password forgotten?</a><br/>
</div>
<?php
	}
}
?>
		<div class="foot"><a href="<?php echo $url_home; ?>">Go to start page</a></div>
	</body>
</html>