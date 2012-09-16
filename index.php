<!DOCTYPE html>
<html>
	<head>
		<title>awwni.me repost finder</title>
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.0.js"></script>
		<style type="text/css">
			* { margin:0; padding:0; }
			a img { border:0; }
			ul { list-style:none; }
			html { height:100%; }
			body { background:url(../stars.jpg); font-family:sans-serif; text-align:center; margin:0; padding:200px 0 0; overflow:hidden; height:100%; }
			
			h1 { width:200px; height:0; padding:150px 0 0; overflow:hidden; background:url(../logo<?php echo rand() % 5 + 1; ?>.png) no-repeat; position:absolute; top:0; left:0; z-index:100; }
			h2 { font-size:32px; }
			
			#intro { text-transform:uppercase; }
			#intro .buttons { margin-top:20px; }
			#intro span { padding:10px 20px; background:#91a9c5; font:bold 16px sans-serif; color:#fff; cursor:pointer; }
			
			.forms { overflow:hidden; width:810px; margin:0 auto; }
			.form { padding:20px; margin:20px; float:left; background:#fff; border-radius:5px; }
			.form label { position:absolute; top:-9999px; left:-9999px; }
			.form input[type="text"] { padding:5px; width:250px; }
			.form h3 { margin:0 0 10px; text-transform:uppercase; }
			.form button { padding:5px 10px; }
			
			#message { margin:20px auto; padding:10px; width:500px; background:#fff; border-radius:5px; display:none; text-align:center; }
			#message.error { background:#f66; color:#fff; }
			
			#results { width:1100px; margin:0 auto; display:none; }
			#results a { text-decoration:none; display:block; height:180px; overflow:hidden; }
			#results img { max-width:180px; }
			#results li { position:relative; background:#fff url(images/ajax.gif) no-repeat center center; float:left; margin:10px; width:180px; padding:10px; border-radius:5px; }
			#results li:hover { background:#91a9c5; }
			#results p { background:#000; color:#fff; padding:5px; position:absolute; bottom:10px; right:10px; }
			
			footer p { position:absolute; bottom:10px; }
			footer .created { left:10px; }
			footer .powered { right:10px; }
			footer iframe { display:none; }
			
		</style>
	</head>
	<body>
		<h1>awwni.me</h1>
		<div id="intro">
			<h2>awwni.me post finder</h2>
			<p>Want to see if something's been posted on r/awwnime? Find out!</p>
		</div>
		<div class="forms">
			<div id="urlCheck" class="form">
				<h3>Find by URL</h3>
				<label for="txtUrl">Image URL:</label>
				<input type="text" id="txtUrl" />
				<button id="btnUrl">Check</button>
			</div>
			<div id="uploadCheck" class="form">
				<h3>Find by Upload</h3>
				<form action="match.php" target="ifUpload" method="post" enctype="multipart/form-data">
					<label for="txtFile">File:</label>
					<input type="file" name="txtFile" />
					<button type="submit" id="btnUpload">Upload</button>
				</form>
			</div>
		</div>
		<ul id="results">
			
		</ul>
		<p id="message"></p>
		<footer>
			<p class="created">Created by <a href="http://dxprog.com" target="_blank">dxprog</a></p>
			<p class="powered">Powered by <a href="http://www.reddit.com/r/awwnime" target="_blank">r/awwnime</a></p>
			<iframe id="ifUpload" name="ifUpload"></iframe>
		</footer>
		<script type="text/javascript">
			(function() {
			
				var
				
				$results = $('#results'),
				$message = $('#message'),
				
				resultsCallback = function(data) {
					if (data) {
						var
						i = 0,
						count = data.matches.length,
						out = '',
						match = null;
						
						out += '<li><a href="' + data.original + '" target="_blank"><img src="thumb.php?file=' + escape(data.original) + '&width=180&height=180" alt="Original Image" /><p>Original</p></a></li>';
						for (; i < count; i++) {
							match = data.matches[i];
							out += '<li><a href="http://www.reddit.com/r/awwnime/comments/' + match.id + '/" target="_blank"><img src="thumb.php?file=' + escape(match.url) + '&width=180&height=180" alt="' + match.title + '" /><p>' + Math.round(match.similarity) + '% match</p></a></li>';
						}
						$message.fadeOut(400, function() {
							$results.html(out).fadeIn();
						});
					}
				},
				
				uploadClick = function(e) {
					$results.fadeOut(400, function() {
						$message.html('Uploading...').fadeIn();
					});
				},
				
				urlClick = function(e) {
					$results.fadeOut(400, function() {
						$message.html('Checking...').fadeIn();
						$.ajax({
							url:'match.php?url=' + escape($('#txtUrl').val()),
							dataType:'json',
							success:resultsCallback
						});
					});
				},
				
				init = (function() {
					$('#btnUrl').on('click', urlClick);
					$('#btnUpload').on('click', uploadClick);
					window.resultsCallback = resultsCallback;
				}());
			
			}());
			var _gaq=_gaq||[];_gaq.push(["_setAccount","UA-280226-6"]);_gaq.push(["_trackPageview"]);(function(){var a=document.createElement("script");a.type="text/javascript";a.async=!0;a.src=("https:"==document.location.protocol?"https://ssl":"http://www")+".google-analytics.com/ga.js";var b=document.getElementsByTagName("script")[0];b.parentNode.insertBefore(a,b)})();
		</script>
	</body>
</html>