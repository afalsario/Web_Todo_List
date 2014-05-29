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

	function savefile($filename, $array)
	{
	    $handle = fopen($filename, 'w');
	        foreach ($array as $value)
	        {
	        	fwrite($handle, $value . PHP_EOL);
	        }
	    fclose($handle);
	}

	// echo "<h1>GET</h1>";
	// var_dump($_GET);
	// echo "<br>";
	// echo "<h1>POST</h1>";
	// var_dump($_POST);
	// echo "<br>";
?>
<html>
<head>
	<title>TODO List</title>
</head>
<body>
	<h2>TODO List</h2>
	<ul>
	<?php
		//load file with list
		$items = load_file(FILENAME, $items);
		//if the post is not empty, add item to the list array
		if(!empty($_POST))
		{
			$items[] = $_POST['new_item'];
		}

		//if the user clicks the link to remove and item, remove item and reindex the array
		if(isset($_GET['index']))
		{
				$index = $_GET['index'];
				unset($items[$index]);
				$items = array_values($items);
		}

		//display each item in the array
		foreach($items as $key => $item)
		{
			echo "<li>" . $item . " " . "<a href=\"todo_list.php?index=" . $key . "\">Mark Complete</a></li><br>";
		}

		//save/overwrite the defined file with added items
		savefile(FILENAME, $items);
	?>
	</ul>

	<!-- form to post new items to the existing array -->
	<form method="POST">
		<p>
			<label for="new_item">New Item:</label>
			<br>
			<input type="text" id="new_item" name="new_item">
		</p>
		<input type="submit" value="Add">
	</form>
</body>
</html>
