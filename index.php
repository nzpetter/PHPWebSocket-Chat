<!DOCTYPE HTML>
<?php session_start();?>
<?php $_SESSION['token'] = md5(uniqid(mt_rand(),true));?>
<html>
	<head>
		<meta charset='UTF-8' />
		<title>Websockets chat</title>
		<style>
			input, textarea {border:1px solid #CCC;margin:0px;padding:0px}

			#body {max-width:800px;margin:auto}
			#log {width:100%;height:400px}
			#message {width:100%;line-height:20px}
		</style>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script src="fancywebsocket.js"></script>
		<script>
			var Server;

			function log(text) {
				$log = $('#log');
				//Add text to log
				$log.append(($log.val() ? "\n" : '') + text);
				//Autoscroll
				$log[0].scrollTop = $log[0].scrollHeight - $log[0].clientHeight;
			}

			function send(text) {
				Server.send('message', text);
			}
			function base64_encode(data) {
				//  discuss at: http://phpjs.org/functions/base64_encode/
				// original by: Tyler Akins (http://rumkin.com)
				var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
				var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
						ac = 0,
						enc = '',
						tmp_arr = [];

				if (!data) {
					return data;
				}

				do { // pack three octets into four hexets
					o1 = data.charCodeAt(i++);
					o2 = data.charCodeAt(i++);
					o3 = data.charCodeAt(i++);

					bits = o1 << 16 | o2 << 8 | o3;

					h1 = bits >> 18 & 0x3f;
					h2 = bits >> 12 & 0x3f;
					h3 = bits >> 6 & 0x3f;
					h4 = bits & 0x3f;

					// use hexets to index into b64, and append result to encoded string
					tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
				} while (i < data.length);

				enc = tmp_arr.join('');

				var r = data.length % 3;

				return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);
			}

			$(document).ready(function () {
				log('Connecting...');
				Server = new FancyWebSocket('ws://127.0.0.1:9300/');
				message = new Object();
				message.token = '<?php echo $_SESSION['token'];?>';

				$('#message').keypress(function (e) {
					if (e.keyCode === 13 && this.value) {
						var parentValue = this.value;
						log('You: ' + this.value);
						message.value = this.value;
						send(base64_encode(JSON.stringify(message)));

						$(this).val('');
					}
				});

				//Let the user know we're connected
				Server.bind('open', function () {
					log("Connected.");
				});

				//OH NOES! Disconnection occurred.
				Server.bind('close', function (data) {
					log("Disconnected.");
				});

				//Log any messages sent from server
				Server.bind('message', function (payload) {
					log(payload);
				});

				Server.connect();
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