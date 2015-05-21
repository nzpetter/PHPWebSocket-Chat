var Server = function (url, username) {
	this.server = new FancyWebSocket(url);
	this.username = username;
};

Server.prototype.connect = function () {
	this.server.connect();
	this.server.bind('open', function () {
		Server.log("Connected.");
	});
	this.server.bind('close', function (data) {
		Server.log("Disconnected.");
	});

	this.server.bind('message', function (payload) {
		Server.log(payload);
	});
};

Server.base64encode = function (data) {
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
};

Server.prototype.sendMessage = function (token, message) {
	if (token.length === 0) {
		Server.log('ERROR! Your token is empty');
		return false;
	}
	var messageobject = new Object();
	messageobject.token = token;
	messageobject.message = message;
	this.server.send('message', Server.base64encode(JSON.stringify(messageobject)));
};

Server.prototype.sendCommand = function (token, command) {
	if (token.length === 0) {
		Server.log('ERROR! Your token is empty');
		return false;
	}
	var messageobject = new Object();
	messageobject.token = token;
	messageobject.command = command;
	this.server.send('message', Server.base64encode(JSON.stringify(messageobject)));
}

Server.log = function (text) {
	if ($('#log').length > 0) {
		$log = $('#log');
		//Add text to log
		$log.append(($log.val() ? "\n" : '') + text);
		//Autoscroll
		$log[0].scrollTop = $log[0].scrollHeight - $log[0].clientHeight;
	}
};

Server.prototype.logUserMessage = function (text) {
	if ($('#log').length > 0) {
		$log = $('#log');
		//Add text to log
		$log.append(($log.val() ? "\n" : '') + this.username + " " + text);
		//Autoscroll
		$log[0].scrollTop = $log[0].scrollHeight - $log[0].clientHeight;
	}
};


