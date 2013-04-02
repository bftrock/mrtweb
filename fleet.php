<?php
require_once ("inc/util.mrt.php");
require_once ("inc/class.database.php");
require_once ("inc/class.user.php");

$db = new database();
$db->open_connection();

session_start();
$user = new user($db);
$user->check_session();
if ($user->in_session) {
	$user->load(user::LOAD_DB);
} else {
	die("Error: user must be logged in to access this page.");
}

?>
<html>

<head>

	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<link href='http://fonts.googleapis.com/css?family=Dosis:400,600|Quattrocento+Sans:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="/css/mrt.css" />
	<title>MyRemoteTunnel: Fleet</title>
	<style type="text/css">

#header {
	margin-bottom: 20px;
}

#content {
	width: 770px;
	min-height: 600px;
}

#fleet {
	width: 100%;
}

#fleet th, #fleet td {
	padding: 5px;
}

#fleet .online {
	background-color: lime;
	border: solid 1px black;
	border-radius: 5px;
	width: 60px;
}

#fleet .offline {
	background-color: gray;
	border: solid 1px black;
	border-radius: 5px;
	width: 60px;
}
	</style>

</head>
<body>
<div id="container">

	<div id="login">
		<p><a href="/user.php"><?php echo $user->name; ?></a>&nbsp;|&nbsp;<a href="index.php?m=lo">Log out</a></p>
	</div>
	
	<div id="header">
		<h1><a href="/index.php">MyRemoteTunnel</a></h1>
	</div>

	<div id="content">
		<h3>My Fleet</h3>
		<table id="fleet">
			<tr><th>Status</th><th>Device Name</th><th>Serial #</th></tr>
<?php
$user->get_devices();
foreach ($user->devices as $device) {
	if ($device->status == 0) {
		$class = "offline";
	} elseif ($device->status == 1) {
		$class = "online";
	}
	echo "<tr><td class=\"{$class}\">".$device->get_status_name()."</td><td>{$device->name}</td><td>{$device->serial_number}</td></tr>\n";
}
?>
		</table>
	</div>

</div>

</body>
</html>
