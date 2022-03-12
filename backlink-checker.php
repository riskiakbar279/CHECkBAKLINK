<?php
ob_start();
$backlinks = trim(file_get_contents('backlinks.txt'));
$backlinks = explode("\n", $backlinks);
if (array() == $backlinks) {
	echo 'No backlink.';
	exit();
}

$t = array();
foreach ($backlinks as $b) {
	$b = trim($b);
	if (!preg_match('/^http(s)?\:\/\//i', $b)) {
		$b = 'http://'.$b;
	}
	$t[] = $b;
}
$backlinks = $t;
unset($t);
?>
<html>
<head>
<style type="text/css">
.no {
	color:#F00;
	font-weight:900;
}
.ok {
	color:#090;
	font-weight:900;
}
</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript">
var _index = 0;
var max_backlinks = <?php echo count($backlinks); ?>;
var dead_backlinks = 0;
var ok_backlinks = 0;
var no_backlinks = 0;

var backlinks = ["<?php echo implode('", "', $backlinks); ?>"];

function check_backlink() {
	i = _index;
	_backlink = backlinks[i];
	console.log(i);
	console.log(_backlink);
	console.log('url='+encodeURI( _backlink))
	$.ajax({
		url: 'backlink-checker-ajax.php',
		type: 'POST',
		data: 'url='+encodeURI( _backlink),
		dataType: 'json',
		cache: false
	}).done(function( data ) {
		console.log(i + ' = done');
		if ('dead' == data.status) {
			dead_backlinks++;
			$('#status_'+i).html( data.message ).addClass('no');
		} else if ('ok' == data.status) {
			ok_backlinks++;
			$('#status_'+i).html( data.message ).addClass('ok');
		} else {
			no_backlinks++;
			$('#status_'+i).html( data.message ).addClass('no');
		}
		$('#ok').html(ok_backlinks);
		$('#no').html(no_backlinks);
		$('#dead').html(dead_backlinks);
		if (i < max_backlinks) {
			_index++;
			check_backlink();
		}
	});
}

$(document).ready(function() {
	check_backlink();
});
</script>
</head>
<body>
<p>Checking <strong><?php echo number_format(count($backlinks)); ?></strong> links.</p>
<p>Ok = <span id="ok" class="ok">0</span>. No = <span id="no" class="no">0</span>. Dead = <span id="dead" class="no">0</span></p>
<table border="1" cellpadding="5" cellspacing="0">
<tr>
	<th>Link</th>
	<th>Status</th>
</tr>
<?php $i = 0; foreach ($backlinks as $b) : ?>
<tr>
	<td><input type="text" size="100" value="<?php echo htmlentities($b); ?>" readonly="1" onclick="this.select()" /> <a href="<?php echo htmlentities($b); ?>" target="_blank">Open</a></td>
	<td id="status_<?php echo $i; ?>"></td>
</tr>
<?php $i++; endforeach; ?>
</table>
</body>
</html>
