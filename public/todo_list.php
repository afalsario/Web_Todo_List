<?
	// define('FILENAME', 'new.txt');
	$file_name = 'new.txt';
	$items = [];
	$errorMessage = '';

	function load_file($filename, $array)
	{
		if(is_readable($filename) && filesize($filename) > 0)
		{
			$handle = fopen($filename, 'r');
		    $contents = trim(fread($handle, filesize($filename)));
		    $list = explode(PHP_EOL, $contents);
		    foreach ($list as $value)
	        {
	            array_push($array, $value);
	        }
	    fclose($handle);
	    return $array;
		}
		else
		{
			return array();
		}
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
	// var_dump($_FILES);

	//load file with list
	$items = load_file($file_name, $items);
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

	if(isset($_FILES['file1']) && ($_FILES['file1']['type'] !== 'text/plain'))
	{
		$errorMessage = "ERROR: Please use a plain text file";
	}
	elseif(count($_FILES) > 0 && $_FILES['file1']['error'] == 0)
	{
		//set destination for uploaded files
		$upload_dir = '/vagrant/sites/todo.dev/public/uploads/';
		//get the file name using basename
		$filename = basename($_FILES['file1']['name']);
		// Create the saved filename using the file's original name and our upload directory
    	$saved_filename = $upload_dir . $filename;
    	// Move the file from the temp location to our uploads directory
    	move_uploaded_file($_FILES['file1']['tmp_name'], $saved_filename);
    	$items = load_file($saved_filename, $items);
	}
	//save/overwrite the defined file with added items
	savefile($file_name, $items);
?>
<html>
<head>
	<title>TODO List</title>
</head>
<body>
	<h2>TODO List</h2>
	<ul>
	<!-- display each item in the array -->
	<? foreach($items as $key => $item): ?>
		<li> <?= $item . " " . "<a href=\"todo_list.php?index={$key}\">Remove Item</a>";?></li><br>
	<? endforeach; ?>
	</ul>
	<!-- form to post new items to the existing array -->
	<form method="POST" action="/todo_list.php">
		<p>
			<label for="new_item">New Item:</label>
			<input type="text" id="new_item" name="new_item" autofocus="autofocus">
			<input type="submit" value="Add">
		</p>
	</form>
	<h3>Upload File</h3>
	<? if (!empty($errorMessage)): ?>
	    <p> <?= $errorMessage; ?> </p>
	<? endif; ?>
	<form method="POST" enctype="multipart/form-data">
	<p>
		<input type="file" id="file1" name="file1">
		<input type="submit" value="Upload">
	</p>
	</form>
</body>
</html>
