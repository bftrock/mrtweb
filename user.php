<?php

require_once ("inc/class.database.php");
require_once ("inc/util.mrt.php");
require_once ("inc/class.user.php");

$db = new database();
$db->open_connection();

session_start();
$user = new user($db);
$user->check_session();
if (!$user->in_session) {
	die("Error: user must be logged in to access this page.");
}

if (check_field("m", $_REQUEST)) {
	$mode = $_REQUEST['m'];
} else {
	$mode = "e";
}

$msg = "";
$border_color = "";
$display = "none";
if (check_field("em", $_REQUEST)) {
	$msg = "<p class=\"bold\">Error</p><p>{$_REQUEST['em']}</p>\n";
	$border_color = "#ff0000";
	$display = "block";
}

if (check_field("sm", $_REQUEST)) {
	$msg = "<p class=\"bold\">Success</p><p>{$_REQUEST['sm']}</p>\n";
	$border_color = "#00ff00";
	$display = "block";
}

switch ($mode) {

	case "e":	// edit
		$user->load(user::LOAD_DB);
		break;
	
	case "p":	// edit with password change
		$user->load(user::LOAD_DB);
		break;
	
	case "u":	// update
		$user->name = $_POST['name'];
		$user->email = $_POST['email'];
		if (isset($_POST['old_password'])) {
			if ($user->verify_password($_POST['old_password'])) {
				$user->password = $_POST['new_password'];
				$user->update();
				$msg = "The account information was successfully updated.";
				$url = "user.php?m=e&sm=" . urlencode($msg);
				header("Location:{$url}");
			} else {
				$msg = "The old password you supplied does not match the stored password.";
				$url = "user.php?m=p&em=" . urlencode($msg);
				header("Location:{$url}");
			}
		} else {
			$user->update();
			$msg = "The account information was successfully updated.";
			$url = "user.php?m=e&sm=" . urlencode($msg);
			header("Location:{$url}");
		}
		break;
	
	default:
}

?>
<html>

<head>

	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<link href='http://fonts.googleapis.com/css?family=Dosis:400,600|Quattrocento+Sans:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="css/mrt.css" />
	<title>MyRemoteTunnel: User</title>
	<style type="text/css">

#header {
	margin-bottom: 100px;
}

#content {
	width: 350px;
}

#msg {
	border-color: <?php echo $border_color; ?>;
	display: <?php echo $display; ?>;
}

	</style>

</head>

<body>

<div id="container">

	<div id="login">
		<p><a href="user.php"><?php echo $user->name; ?></a>&nbsp;|&nbsp;<a href="index.php?m=lo">Log out</a></p>
	</div>
	
	<div id="header">
		<h1><a href="index.php">MyRemoteTunnel</a></h1>
	</div>


	<div id="content">

		<p><a href="fleet.php"><< View Fleet</a></p>
		<h3>Account Information</h3>
		<form method="post" action="user.php?m=u" id="user_form">
			<table>
				<tr><td id="name_label">Name*:</td>
					<td><input type="text" size="25" name="name" id="name" value="<?php echo $user->name; ?>"/></td></tr>
				<tr><td id="email_label">Email address*:</td>
					<td><input type="text" size="25" name="email" id="email"  value="<?php echo $user->email; ?>"/></td></tr>
<?php if ($mode == "e" || $mode == "u") { ?>
				<tr><td></td><td><a href="user.php?m=p">Change password</a></td></tr>
<?php } elseif ($mode == "p") { ?>
				<tr><td id="password1_label">Old password*:</td>
					<td><input type="password" size="25" name="old_password" id="password1" /></td></tr>
				<tr><td id="password2_label">New password*:</td>
					<td><input type="password" size="25" name="new_password" id="password2" /></td></tr>
				<tr><td id="password3_label">Re-enter new password*:</td>
					<td><input type="password" size="25" id="password3" /></td></tr>
<?php } ?>
				<tr><td></td>
					<td><input type="button" value="Save" onclick="submitForm()" /></td></tr>
			</table>
		</form>

	</div>
	
	<div id="msg">
		<?php echo $msg; ?>
	</div>

</div>

<script src="js/jquery.js"></script>
<script src="js/mrt.js"></script>
<script type="text/javascript">

<?php if ($mode == "e") { ?>
function submitForm() {
	
	var formId = 'user_form';
	try {
		var reqFields = new Array('name', 'email');
		var i;
		for (i in reqFields) {
			var fieldId = reqFields[i];
			var x = document.forms[formId][fieldId].value;
			if (x == null || x == '') {
				highlightField(fieldId + '_label');
				throw 1;
			}
		}
		fieldId = 'email';
		var x = document.forms[formId][fieldId].value;
		var atpos = x.indexOf('@');
		var dotpos = x.lastIndexOf('.');
		if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= x.length) {
			throw 2;
		}
		document.forms[formId].submit();
	} catch (err) {
		var errMsg;
		switch (err) {
			case 1:
				errMsg = 'You must fill out all required fields.';
				break;
			case 2:
				errMsg = 'You must enter a valid email address.';
				break;
		}
		displayMsg('Error', errMsg, '#ff0000');
	}
	
}
<?php } else { ?>
function submitForm() {
	
	var formId = 'user_form';
	try {
		var reqFields = new Array('name', 'email', 'password1', 'password2', 'password3');
		var i;
		for (i in reqFields) {
			var fieldId = reqFields[i];
			var x = document.forms[formId][fieldId].value;
			if (x == null || x == '') {
				highlightField(fieldId + '_label');
				throw 1;
			}
		}
		fieldId = 'email';
		var x = document.forms[formId][fieldId].value;
		var atpos = x.indexOf('@');
		var dotpos = x.lastIndexOf('.');
		if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= x.length) {
			throw 2;
		}
		var x = document.forms[formId]['password2'].value;
		if (x.search("[a-zA-Z]") == -1 || x.search("[0-9]+") == -1 || x.length < 7) {
			throw 3;
		}
		var x = document.forms[formId]['password2'].value;
		var x2 = document.forms[formId]['password3'].value;
		if (x != x2) {
			throw 4;
		}
		document.forms[formId].submit();
	} catch (err) {
		var errMsg;
		switch (err) {
			case 1:
				errMsg = 'You must fill out all required fields.';
				break;
			case 2:
				errMsg = 'You must enter a valid email address.';
				break;
			case 3:
				errMsg = 'Passwords must contain at least 7 characters, at least 1 letter, and at least 1 number.'
				break;
			case 4:
				errMsg = 'The passwords must match.';
				break;
		}
		displayMsg('Error', errMsg, '#ff0000');
	}
	
}
<?php } ?>
</script>
</body>

</html>
