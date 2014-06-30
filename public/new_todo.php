<?php
$items_per_page = 5;

class InvalidInputException extends Exception{}

function getOffset($items_per_page)
{
    $page = isset($_GET['page'])? $_GET['page'] : 1;
    return ($page - 1) * $items_per_page;
}

//-------------1. Establish DB Connection
$dbc = new PDO('mysql:host=127.0.0.1;dbname=todo_db', 'ashley', 'password');

// Tell PDO to throw exceptions on error
$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try
{
//-------------2. Check if something was posted
    if(isset($_POST['new_item']))
    {
        //-------------a. Is item being added? => Add todo!
        if((strlen($_POST['new_item']) != 0 && (strlen($_POST['new_item'])) <= 240))
        {
            $stmt = $dbc->prepare('INSERT INTO todo_list (todo) VALUES (:todo)');
            $stmt->bindValue(':todo', htmlspecialchars(strip_tags($_POST['new_item'])), PDO::PARAM_STR);
            $stmt->execute();
        }
        else
        {
            throw new InvalidInputException("Error! Please enter a todo longer than 0 characters and less than 240");
        }
    }
    //-------------b. Is item being removed? => Remove it! As a post
    if(isset($_POST['remove_id']))
    {
        $dbc->query('DELETE FROM todo_list WHERE id = ' . $_POST['remove_id']);
    }
}
catch (InvalidInputException $e)
{
    $e->getMessage();
}

 if(isset($_POST['reset']))
    {
        $dbc->query('TRUNCATE TABLE todo_list;');
    }

//-------------3. Query DB for total todo count.
$count = $dbc->query('SELECT count(*) FROM todo_list')->fetchColumn();
//divides the count by the items/page to determine total pages and show the proper pagination links
$numPages = ceil($count / $items_per_page);

//-------------4. Determine pagination values.
$page = isset($_GET['page'])?$_GET['page']: 1;
$previous_page = $page - 1;
$next_page = $page + 1;

//preparing data from the table so that it shows a limit of items and an offset for pagination
$query = 'SELECT * FROM todo_list LIMIT :limit OFFSET :offset';
$stmt = $dbc->prepare($query);
$stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', getOffset($items_per_page), PDO::PARAM_INT);
$stmt->execute();

//-------------5. Query for todos on current page.
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<html>
<head>
    <title>TODO List</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/todo_list.css">
</head>
<body>
    <h2>ToDo List</h2>
    <? if(isset($e)): ?>
        <p style="color:red"> <?= $e->getMessage(); ?> </p>
    <? endif; ?>

    <div class="container">
        <table class="table table-striped">
            <ul>
                <!-- display each item in the table -->
                <? foreach($list as $item): ?>
                    <tr>
                        <td><?= $item['id']; ?></td>
                        <td><?= $item['todo'] ?> </td>
                        <td><button class="btn btn-danger btn-remove" data-todo="<?= $item['id']; ?>">Remove</button></td>
                    </tr>
                <? endforeach; ?>
            </ul>
        </table>
    </div>

    <ul class="pager">
            <? if(getOFFSET($items_per_page) !== 0): ?>
        <li><?= "<a href=\"?page={$previous_page}\">Previous</a>"; ?></li>
            <? endif; ?>
            <? if($numPages > $page): ?>
        <li><?= "<a href=\"?page={$next_page}\">"; ?>Next</a></li>
            <? endif; ?>
    </ul>

    <!-- form to post new items to the database-->
    <div id="add_form">
        <form method="POST" action="#">
            <p>
                <label for="new_item">New Item:</label>
                <input type="text" id="new_item" name="new_item" autofocus="autofocus" value="">
                <button class="btn btn-primary">Add Item</button>
            </p>
        </form>
    </div>

    <form method="POST" action="#" id="remove_form">
        <input type="hidden" name="remove_id" id="remove_id"  value="">
    </form>

    <form method="POST" action="/new_todo.php" id="reset_form">
        <input type="hidden" name="reset" value="true">
        <button name="reset" class="btn-reset btn btn-warning">Reset</button>
    </form>

    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script>
        $('.btn-remove').click(function() {
            var todoId = $(this).data('todo');
            if(confirm('Are you sure you want to remove item ' + todoId + '?'))
            {
                $('#remove_id').val(todoId);
                $('#remove_form').submit();
            }
        });

        $('.btn-reset').click(function(e) {
            e.preventDefault();
            if(confirm('Are you sure you want to reset?'))
            {
                $('#reset_form').submit();
            }
        });
    </script>

</body>
</html>
