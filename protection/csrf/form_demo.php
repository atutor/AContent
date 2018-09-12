<?php 
session_start(); // Called before loading the class!
require_once 'class_csrf.php';


if (isset($_POST['submit']))
{
	$username = trim(strip_tags($_POST['username']));
	$password = trim(strip_tags($_POST['password']));
	
	$errors = array();
	$messages = array();
	
	if (Token::isValid() AND Token::isRecent())
	{
	
		if (empty($username))
		{
			$errors[] = 'Username cannot be blank.';
		}
		
		if (empty($password))
		{
			$errors[] = 'Password cannot be blank.';
		}
		
		$messages[] = 'Security Token was valid.';
	}
	else
	{
		// Invalid security token
		$errors[] = 'Invalid Security Token';
		
		// You could use the exitOnFailure method or handle it your own way.
		
	}
}


 ?>
<!doctype html>
<html>
	<head>
		<title>CSRF Class Form Demo</title>
	</head>
	
	<body>
		<h2>CSRF Class Form Demo</h2>
		<?php
		if (@$errors)
		{
			foreach ($errors as $error)
			{
				echo '<span style="color: red;">' . $error . '</span><br />';
			}
			echo '<br />';
		}
		
		if (@$messages)
		{
			foreach ($messages as $message)
			{
				echo '<span style="color: green;">' . $message . '</span><br />';
			}
			echo '<br />';
		}
		?>
		<form action="" method="post" autocomplete="off">
			<label for="username">Username:</label>
			<input type="text" name="username" id="username" required />
			<br />
			<label for="password">Password:</label>
			<input type="password" name="password" id="password" required />
			<!-- Comment this out for an "invalid security token" example. -->
			<?php echo Token::display(); ?><br /><br />
			<input type="submit" name="submit" value="Login" />
		</form>
	</body>
</html>