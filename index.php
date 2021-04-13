<!-- Home page. App starts here. -->
<?php
include('config.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="<?php echo $design; ?>/style.css" rel="stylesheet" title="Style" />
        <title>Members Area</title>
    </head>
    <body>
    	<div class="header">
			<h1><a href="<?php echo $url_home;?>">Cybersecurity Capstone Project</a></h1>
	    </div>
        <div class="content">

<?php
//If the user is logged, we display it username
if(isset($_SESSION['username'])) 
{
	echo ' '.htmlentities($_SESSION['username'], ENT_QUOTES, 'UTF-8');
	echo '<br>';
}
?>

Welcome to the Cybersecurity Capstone Project (University of Maryland/Coursera) page.
<br>
<br>

<?php
//If the user is logged, we display links to edit his infos, to see his pms and to log out
if (isset($_SESSION['username'])) {
	echo 'You can <a href="users.php">see the list of users</a>.<br /><br />';

	//We count the number of new messages the user has
	$nb_new_pm = mysqli_fetch_array(mysqli_query($link, 'select count(*) as nb_new_pm from pm where ((user1="'.$_SESSION['userid'].'" and user1read="no") or (user2="'.$_SESSION['userid'].'" and user2read="no")) and id2="1"'));
	//The number of new messages is in the variable $nb_new_pm
	$nb_new_pm = $nb_new_pm['nb_new_pm'];
	//We display the links
?>

<a href="edit_infos.php">Edit my personnal information</a><br>
<a href="list_pm.php">My personnal messages (<?php echo $nb_new_pm; ?> unread)</a><br>
<a href="connexion.php">Logout</a>
<?php
}
else {
//Otherwise, we display a link to log in and to Sign up
?>
<div class="content">
	<form action="sign_up.php" method="post">
		<input type="submit" value="Sign up" />
	</form>
</div>
<button onclick="window.location.href = 'connexion.php';">Log in</button><br>
<?php
}
?>
		</div>
	</body>
</html>