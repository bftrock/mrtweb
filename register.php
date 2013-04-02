<?php

require_once ("inc/class.database.php");
require_once ("inc/util.mrt.php");
require_once ("inc/class.user.php");

$db = new database();
$db->open_connection();

$name = "";
$email = "";
$err_msg = "";
$display = "none";
if (check_field("m", $_REQUEST)) {
	if ($_REQUEST['m'] == "i") {
		$user = new user($db);
		$user->load(user::LOAD_POST);
		if ($user->email_available()) {
			$user->insert();
			header("Location:index.php?m=nr&email=" . urlencode($user->email));
		} else {
			$err_msg = "Submitted email address ({$user->email}) is already in use. Please try another one.";
			$url = "register.php?m=e&em=" . urlencode($err_msg);
			header("Location:{$url}");
		}
	} elseif ($_REQUEST['m'] == "e") {
		$err_msg = $_REQUEST['em'];
		$display = "block";
	}
}

?>
<html>

<head>

	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<link href='http://fonts.googleapis.com/css?family=Dosis:400,600|Quattrocento+Sans:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="css/mrt.css" />
	<title>MyRemoteTunnel: Register</title>
	<style type="text/css">

#header {
	margin-bottom: 100px;
}

#content {
	width: 350px;
}

#err_msg {
	border-color: #ff0000;
	display: <?php echo $display; ?>;
}

	</style>

</head>

<body>

<div id="container">

	<div id="header">
		<h1><a href="index.php">MyRemoteTunnel</a></h1>
	</div>


	<div id="content">

		<h3>Register</h3>
		<form method="post" action="register.php?m=i" id="user_form">
			<table>
				<tr><td id="name_label">Name*:</td>
					<td><input type="text" size="25" name="name" id="name" /></td></tr>
				<tr><td id="email1_label">Email address*:</td>
					<td><input type="text" size="25" name="email" id="email1" /></td></tr>
				<tr><td id="email2_label">Re-enter email address*:</td>
					<td><input type="text" size="25" id="email2" /></td></tr>
				<tr><td id="password1_label">Password*:</td>
					<td><input type="password" size="25" name="password" id="password1" /></td></tr>
				<tr><td id="password2_label">Re-enter password*:</td>
					<td><input type="password" size="25" id="password2" /></td></tr>
				<tr><td></td>
					<td><input type="button" value="Save" onclick="submitForm()" /></td></tr>
			</table>
		</form>

	</div>
	
	<div id="err_msg">
		<p class="bold">Error</p><p><?php echo $err_msg; ?></p>
	</div>

</div>

<script src="js/jquery.js"></script>
<script src="js/mrt.js"></script>
<script type="text/javascript">

function submitForm() {
	
	var formId = 'user_form';
	try {
		var reqFields = new Array('name', 'email1', 'email2', 'password1', 'password2');
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
		var x = document.forms[formId]['email1'].value;
		var x2 = document.forms[formId]['email2'].value;
		if (x != x2) {
			throw 3;
		}
		var x = document.forms[formId]['password1'].value;
		if (x.search("[a-zA-Z]") == -1 || x.search("[0-9]+") == -1 || x.length < 7) {
			throw 4;
		}
		var x = document.forms[formId]['password1'].value;
		var x2 = document.forms[formId]['password2'].value;
		if (x != x2) {
			throw 5;
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
				errMsg = 'The emails must match.'
				break;
			case 4:
				errMsg = 'Passwords must contain at least 7 characters, at least 1 letter, and at least 1 number.'
				break;
			case 5:
				errMsg = 'The passwords must match.';
				break;
		}
		displayErrMsg(errMsg);
	}
	
}
</script>
</body>

</html>
