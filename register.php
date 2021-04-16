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
			<h1>Cybersecurity Capstone Project</h1>
		</div>
<?php

//We check if the form has been sent
if(isset($_POST['username'], $_POST['password'], $_POST['passverif'], $_POST['email']) and $_POST['username'] != '')
{
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
				$salt	  = (string)rand(10000, 99999);	     //Generate a five digit salt.				
				$password = hash("sha512", $salt.$password); //Compute the hash of salt concatenated to password.
				
				$result = $link->query('select id from users where username="'.$username.'"');
				if ($result != FALSE) {
					/* determine number of rows result set */
					$row_cnt = mysqli_num_rows($result);
					mysqli_free_result($result);
				}
				else
				{
					echo "<script type=\"text/javascript\">alert(\"Last SQL query error: " . $link->error . "\")</script>";
				}
				
				if($row_cnt == 0)
				{
					//We count the number of users to give an ID to this one
					if ($result = $link->query('select id from users')) 
					{
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
					
					if($result = $link->query('insert into users(id, username, password, email, signup_date, salt) values ('.$id.', "'.$username.'", "'.$password.'", "'.$email.'", "'.time().'","'.$salt.'")'))
					{
						//We dont display the form
						$form = false;
						
						// TODO: if executed, program crashes
						//mysqli_free_result($result);
						
						echo "<div class=\"message\">You have successfuly been signed up. You can log in now.<br />";
						echo "<a href=\"access.php\">Login</a></div>";						
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

if ($form) 
{
	//We display the form again
?>
		<div class="content">
			<form action="register.php" method="post">
				Please fill the following form to sign up:<br />
				<p style="font-size:14px;">(Password requires 8 characters minimum and must include at least one number, one lowercase letter, one uppercase letter and one symbol)</p><br />
				<div class="center">
					<label for="username">Username</label><input type="text" name="username" value="<?php if(isset($_POST['username'])){echo htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');} ?>" /><br />
					<label for="email">Email</label><input type="text" name="email" value="<?php if(isset($_POST['email'])){echo htmlentities($_POST['email'], ENT_QUOTES, 'UTF-8');} ?>" /><br />
					<label for="password">Password</label><input type="password" name="password" /><br />
					<label for="passverif">Repeat Password</label><input type="password" name="passverif" /><br /><br />
					Answer the following questions, they are asked for in case you forget your password to setup a new password:<br /> 
					<label for="maidenname" style="width: 300px;">Your mother's maiden name?</label><input type="password" name="maidenname" /><br />
					<label for="maidennamerepeat">Repeat: your mother's maiden name?</label><input type="password" name="maidennamerepeat" /><br />
					<label for="elemschool">What elementary school did you attend?</label><input type="password" name="elemschool" /><br />
					<label for="elemschoolrepeat">Repeat: what elementary school did you attend?</label><input type="password" name="elemschoolrepeat" /><br />
					<label for="road">What is the name of the road you grew up on?</label><input type="password" name="road" /><br />
					<label for="roadrepeat">Repeat: what is the name of the road you grew up on?</label><input type="password" name="roadrepeat" /><br />					
					<input type="submit" value="Register" />
				</div>
			</form>
		</div>
<?php
	//We display a message if necessary
	if(isset($message))
	{
		echo '<br><div class="message">'.$message.'</div>';
	}
}
?>
		<div class="foot"><a href="<?php echo $url_home; ?>">Go to start page</a></div>
	</body>
</html>