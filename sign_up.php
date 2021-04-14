<!-- Register a new user. -->
<?php
include('config.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="<?php echo $design; ?>/style.css" rel="stylesheet" title="Style" />
		<title>Sign up</title>
	</head>
	<body>
		<div class="header">
			<a href="<?php echo $url_home; ?>"><img src="<?php echo $design; ?>/images/logo.png" alt="Members Area" /></a>
		</div>
<?php

// see informations.txt
$sql_table = "CREATE TABLE users (
  id bigint(20) NOT NULL,
  username varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  avatar text NOT NULL,
  signup_date int(10) NOT NULL,
  salt varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8\;";

//Check if table users exists in database
$exists = mysql_query("select 1 from users");
if($exists !== FALSE)
{
    echo '<script type="text/javascript">alert("Table users exists in database.")</script>';
}
else
{
    echo '<script type="text/javascript">alert("Table users does not exist in database, creating it.")</script>';
    if ($link->query($sql_table) === TRUE) 
    {
		echo '<script type="text/javascript">alert("Table users created successfully.")</script>';
    } 
	else 
	{
		echo "<script type=\"text/javascript\">alert(\"Error creating table: " . $link->error . "\")</script>";
	}
}

$message = 'Did not enter if isset() yet.';
//We check if the form has been sent
if(isset($_POST['username'], $_POST['password'], $_POST['passverif'], $_POST['email'], $_POST['avatar']) and $_POST['username'] != '')
{
	// TODO: get_magic_quotes_gpc() schuetzt vor SQL Injektion, aber veraltet und von neuerem PHP nicht mehr unterstuetzt -> crash
	// --> durch modernere Variante ersetzen
	//We remove slashes depending on the configuration
	//if(get_magic_quotes_gpc())
	//{
	$_POST['username']  = stripslashes($_POST['username']);
	$_POST['password']  = stripslashes($_POST['password']);
	$_POST['passverif'] = stripslashes($_POST['passverif']);
	$_POST['email']  	= stripslashes($_POST['email']);
	$_POST['avatar']	= stripslashes($_POST['avatar']);
	//}
	
	//We check if the two passwords are identical
	$errors = [];
	if($_POST['password'] == $_POST['passverif'])
	{		
		//We check if the choosen password is strong enough.
		if(checkPassword($_POST['password'], $errors))
		{		
			//We check if the email form is valid
			if(preg_match('#^(([a-z0-9!\#$%&\\\'*+/=?^_`{|}~-]+\.?)*[a-z0-9!\#$%&\\\'*+/=?^_`{|}~-]+)@(([a-z0-9-_]+\.?)*[a-z0-9-_]+)\.[a-z]{2,}$#i',$_POST['email']))
			{			
				//We protect the variables
				$username = mysqli_real_escape_string($link, $_POST['username']);
				$password = mysqli_real_escape_string($link, $_POST['password']);
				$email	  = mysqli_real_escape_string($link, $_POST['email']);
				$avatar   = mysqli_real_escape_string($link, $_POST['avatar']);				
				$salt	  = (string)rand(10000, 99999);	     //Generate a five digit salt.				
				$password = hash("sha512", $salt.$password); //Compute the hash of salt concatenated to password.
				
				$row_cnt = 0;
				$result = $link->query('select id from users where username="'.$username.'"');
				if ($result != FALSE) {
					echo '<script type="text/javascript">alert("Determining row count now")</script>';
					/* determine number of rows result set */
					$row_cnt = mysqli_num_rows($result);
					/* close result set */
					mysqli_free_result($result);
				}
				else
				{
					echo "<script type=\"text/javascript\">alert(\"Last SQL query error: " . $link->error . "\")</script>";
				}

				//$query_result = mysqli_query($link, 'select id from users where username="'.$username.'"');
				//echo '<script type="text/javascript">alert("Finished query")</script>';
				//$dn = mysqli_num_rows($query_result);
				//echo '<script type="text/javascript">alert("Finished mysqli_num_rows()")</script>';
				
				if($row_cnt == 0)
				{
					echo '<script type="text/javascript">alert("row_cnt is 0")</script>';
					//We count the number of users to give an ID to this one
					$id = 1;
					if ($result = mysqli_query($link, 'select id from users')) {
						/* determine number of rows result set */
						$dn2 = mysqli_num_rows($result);
						/* close result set */
						mysqli_free_result($result);
						$id = $dn2 + 1;
					}
					else
					{
						// show last error
						echo "<script type=\"text/javascript\">alert(\"Last SQL query error: " . $link->error . "\")</script>";
					}

					//$dn2 = mysqli_num_rows(mysqli_query($link, 'select id from users'));
					//$id = $dn2 + 1;
					//We save the informations to the database
					
					if($result = mysqli_query($link, 'insert into users(id, username, password, email, avatar, signup_date, salt) values ('.$id.', "'.$username.'", "'.$password.'", "'.$email.'", "'.$avatar.'", "'.time().'","'.$salt.'")'))
					{
						//We dont display the form
						$form = false;
						mysqli_free_result($result);
?>
		<div class="message">You have successfuly been signed up. You can log in.<br />
		<a href="connexion.php">Log in</a></div>
<?php
					}
					else
					{
						//Otherwise, we say that an error occured
						$form	= true;
						$message = 'An error occurred while signing up.';
						echo "<script type=\"text/javascript\">alert(\"Last SQL query error: " . $link->error . "\")</script>";
					}
				}
				else
				{
					//Otherwise, we say the username is not available
					$form	= true;
					$message = 'The username you want to use is not available, please choose another one.';
				}
			}
			else
			{
				//Otherwise, we say the email is not valid
				$form	= true;
				$message = 'The email you entered is not valid.';
			}
		}
		else
		{
			//Otherwise, we say the password is too weak
			$form	= true;
			$message = '';
			foreach ($errors as $item)
				$message = $message.$item."<BR>";
		}
	}
	else
	{
		//Otherwise, we say the passwords are not identical
		$form	 = true;
		$message = 'The passwords you entered are not identical.';
	}
}
else
{
	$form = true;
}
if ($form) {
	//We display a message if necessary
	if(isset($message)) echo '<div class="message">'.$message.'</div>';

	//We display the form again
?>
		<div class="content">
			<form action="sign_up.php" method="post">
				Please fill the following form to sign up:<br />
				<div class="center">
					<label for="username">Username</label><input type="text" name="username" value="<?php if(isset($_POST['username'])){echo htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');} ?>" /><br />
					<label for="password">Password<span class="small">(8 characters min.)</span></label><input type="password" name="password" /><br />
					<label for="passverif">Password<span class="small">(verification)</span></label><input type="password" name="passverif" /><br />
					<label for="email">Email</label><input type="text" name="email" value="<?php if(isset($_POST['email'])){echo htmlentities($_POST['email'], ENT_QUOTES, 'UTF-8');} ?>" /><br />
					<label for="avatar">Avatar<span class="small">(optional)</span></label><input type="text" name="avatar" value="<?php if(isset($_POST['avatar'])){echo htmlentities($_POST['avatar'], ENT_QUOTES, 'UTF-8');} ?>" /><br />
					<input type="submit" value="Sign up" />
				</div>
			</form>
		</div>
<?php
}
?>
		<div class="foot"><a href="<?php echo $url_home; ?>">Go Home</a></div>
		<br>
		Log: <?php echo $message; ?><br />
	</body>
</html>
