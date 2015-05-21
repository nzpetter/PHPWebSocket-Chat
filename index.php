<!DOCTYPE HTML>
<?php
session_start();
include_once 'hashids.php';
$user_id = rand(1, 10000);
$hashids = new Hashids('W3FG#b47hXxz3523', 11);
$_SESSION['token'] = $hashids->encrypt($user_id);
$_SESSION['username'] = 'Piotr Gołasz';
?>
<html>
	<head>
		<meta charset='UTF-8' />
		<title>Websockets chat</title>
		<link rel="stylesheet" href="style.css"/>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script src="fancywebsocket.js"></script>
		<script src="Server.js"></script>
		<script>
			$(document).ready(function () {
				Server.log('Connecting...');
				server = new Server('ws://127.0.0.1:9300/', '<?php echo $_SESSION['username']; ?>');
				server.connect();
				$('#message').keypress(function (e) {
					if (e.keyCode === 13 && this.value) {
						var messageText = this.value;
						server.logUserMessage(this.value);
						server.sendMessage('<?php echo $_SESSION['token']; ?>', messageText);

						$(this).val('');
					}
				});
			});
		</script>
	</head>
	<body>
		<div id='body'>
			<textarea id='log' name='log' readonly='readonly'></textarea><br/>
			<input type='text' id='message' name='message' />
		</div>
	</body>
</html>