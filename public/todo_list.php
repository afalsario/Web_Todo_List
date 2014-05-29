<?php
	define('FILENAME', 'new.txt');

	$items = [];

	function load_file($filename, $array)
	{
		$handle = fopen($filename, 'r');
	    $contents = trim(fread($handle, filesize($filename)));
	    $list = explode("\n", $contents);
	        foreach ($list as $value)
	        {
	            array_push($array, $value);
	        }
	    fclose($handle);
	    return $array;
	}

	function save_file($items, $filename)
	{

	}

?>
<?php
	echo "<h1>GET</h1>";
	var_dump($_GET);
	echo "<br>";
	echo "<h1>POST</h1>";
	var_dump($_POST);
	echo "<br>";
?>
<html>
<head>
	<title>TODO List</title>
</head>
<body>
	<h2>TODO List</h2>
	<ul>
	<?php $items = load_file(FILENAME, $items);
	foreach($items as $item)
	{
		echo "<li>" . $item . "</li><br>";
	}
	?>
	</ul>

	<form method="GET">
		<p>
			<label for="new_item">New Item:</label>
			<br>
			<input type="text" id="new_item" name="new_item">
		</p>
		<input type="submit" value="Add">
	</form>
</body>
</html>
