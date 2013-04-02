<?php

require_once ("inc/class.database.php");
require_once ("inc/util.mrt.php");

if (check_field("m", $_REQUEST)) {
	$mode = $_REQUEST['m'];
} else {
	$mode = "li";
}

$err_msg = null;
$email = "";
switch ($mode) {
	
	case "li":
		break;
	
	case "lo":

		session_start();

		// Unset all of the session variables.
		$_SESSION = array();

		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'],
				$params['secure'], $params['httponly']);
		}

		// Finally, destroy the session.
		session_destroy();
		break;

	case "au":
		$db = new database();
		$db->open_connection();
		$email = $_POST['email'];
		$password = $_POST['password'];
		$sql = "SELECT user_id FROM users WHERE email = '{$email}' AND password = SHA1('{$password}')";
		$db->execute_query($sql);
		if ($db->get_num_rows()) {
			$line = $db->fetch_line();
			$user_id = $line['user_id'];
			session_cache_expire(180);
			session_start();
			$_SESSION['user_id'] = $user_id;
			header("Location:fleet.php");
		} else {
			$err_msg = "The email / password combination was not found. Please try again.";
		}
		break;

	case "nr":	// new registration
		$email = $_REQUEST['email'];
	
	default:
}

?>
<html>

<head>

	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<link href='http://fonts.googleapis.com/css?family=Dosis:400,600|Quattrocento+Sans:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="css/mrt.css" />
	<title>MyRemoteTunnel: Login</title>

	<style type="text/css">

#header {
	margin-bottom: 100px;
}

#login_box {
	background: #f9f9f9;
	border-style: solid;
	border-width: 5px;
	border-color: #dddddd;
	padding: 10px;
	width: 325px;
	margin: 0 auto;
}

#login_box h3 {
	border-bottom-style: solid;
	border-width: 1px;
	border-color: #dddddd;
	margin-top: 0;
	margin-bottom: 20px;
}

#err_msg {
	background: #fff2f2;
	border-style: solid;
	border-width: 2px;
	border-color: #ff0000;
	padding: 10px;
	width: 325px;
	margin: 50px auto;
}

#err_msg .bold {
	margin-top: 0;
}

	</style>

</head>

<body>

<div id="container">

	<div id="header">
		<h1>MyRemoteTunnel</h1>
	</div>
	
	<div id="login_box">
		<h3>Log in</h3>
		<form method="post" action="index.php?m=au">
			<table>
			<tr><td>Email Address:</td><td><input type="text" size="25" name="email" value="<?php echo $email; ?>"/></td></tr>
			<tr><td>Password:</td><td><input type="password" size="25" name="password" /></td></tr>
			<tr><td></td><td><input type="submit" value="Log in" /></td></tr>
			</table>
		</form>
		<a href="register.php">Register</a><a style="float:right" href="#">Reset password</a>
	</div>

<?php if ($err_msg) echo "<div id=\"err_msg\"><p class=\"bold\">Log in error</p><p>{$err_msg}</p></div><p></p>"; ?>
</div>
<script src="js/jquery.js"></script>
</body>

</html>
