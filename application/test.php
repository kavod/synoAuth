<!doctype html>
<html lang="us">
<!-- This is an example javascript in order to show the usage of synoAuth tools -->
<head>
	<meta charset="utf-8">
	<title>Test SynoAuth</title>
	<!-- You HAVE to load JQuery -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js" ></script>
	<!-- Load synoAuth JS script -->
	<script src="synoAuth.js" ></script>
	<script>
		$(function() {
			$.ajaxSetup({
				// Disable caching of AJAX responses
				cache: false
			});

			/***
			 * You have to create a SynoAuth object in order to get synoToken
			 * An asynchronous request will be sent to PHP API
			 */
			$(document).ready(function(){
				$("#content").html(Date.now() + " - SynoToken requis");
				var synoAuth = new SynoAuth();
				synoAuth.getSynoToken();
			});

			/***
			 * Catch the "synoToken" event on $(document) in order to retrieve synoToken response from PHP API
			 */
			$(document).on("synoToken",function(event) {
				msg = event.detail.message;
				alert(msg);
				$("#content").html($("#content").html() + "<br>" + Date.now() + " - " + msg);
			});


			/***
			 * If needed Catch the "login" event on $(document) in order to retrieve user login data
			 */
			$(document).on("login",function(event) {
				msg = event.detail.message + "\nUsername: " + event.detail.username;
				var groups = event.detail.usergroups;
				for (var i in groups)
				{
					msg += "\nUsergroup[" + i + "]:" + groups[i];
				}
				alert(msg);
				$("#content").html($("#content").html() + "<br>" + Date.now() + " - " + msg.replace(/[\n]/gi,"<br>&nbsp;"));
			});
		});
	</script>
</head>
<body>
	<div id="content"></div>
</body>
</html>
