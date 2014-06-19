<?
	// define('FILENAME', 'new.txt');
	$file_name = 'new.txt';
	$items = [];
	$errorMessage = '';

	//require class file
	require('classes/filestore.php');
	$todo = new Filestore($file_name);

	//load file with list
	$items = $todo->read();
	//if the post is not empty, add item to the list array
	if(!empty($_POST))
	{
		$item = trim($_POST['new_item']);
		array_push($items, $item);
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
    	$add_new_file = new Filestore($saved_filename);

    	$new_items = $add_new_file->read();
    	$items = array_merge($items, $new_items);
    	$todo->write($items);
	}
	//save/overwrite the defined file with added items
	$todo->write($items);
?>
<html>
<head>
	<title>TODO List</title>
	<!-- <link rel="stylesheet" href="/css/todo_list.css"> -->
	<!-- <link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'> -->
</head>
<body>
	<header>
		<!-- <img src="/img/todo1.png"> -->
	</header>
	<div class="container">
		<table>
			<ul>
			<!-- display each item in the array -->
			<? foreach($items as $key => $item): ?>
				<tr>
				<td><li> <?= htmlspecialchars(strip_tags($item)) . " "; ?></td> <td><?="<a href=\"todo_list.php?index={$key}\">Remove Item</a>";?></li></td>
				</tr>
			<? endforeach; ?>
			</ul>
		</table>
	</div>

	<!-- form to post new items to the existing array -->
	<footer>
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
			<input type="file" id="file1" name="file1">
			<input type="submit" value="Upload">
		</form>
	</footer>
</body>

</html>
