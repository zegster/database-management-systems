<?php
/* PHP 7.4.2 */
/* Start Session */
session_start();

/* Remove specific session */
unset($_SESSION["database_message"]);
unset($_SESSION["database_message_type"]);
unset($_SESSION["is_edit"]);
unset($_SESSION["row-edit"]);
unset($_SESSION["data_output"]);

/* Database connection */
$con = (require_once "./connection.php");

/* Menu Number and Column Number */
$mn = intval(filter_input(INPUT_GET, "mn"));
$cn = intval(filter_input(INPUT_GET, "cn"));

/* Store table names in an array */
//Iterate through the rows of the result table and sorted it by descending order
$tables = mysqli_query($con, "SHOW TABLES ");
while($result = mysqli_fetch_array($tables))
{
    $tableArray[] = $result["0"];
}
rsort($tableArray);
$table_name = $tableArray[$mn];


/* Processing Table: CREATE */
//Check to see if the create button has been pressed.
if(isset($_POST["create"]))
{
    $columns = mysqli_query($con, "SHOW COLUMNS FROM $table_name");
    while($result = mysqli_fetch_array($columns))
    {
        $userInput[] = filter_input(INPUT_POST, $result["0"]);
    }
    
    $query = "\"".implode( "\", \"", $userInput )."\"";
    mysqli_query($con, "INSERT INTO $table_name 
        VALUES($query)") 
        or die("Error on $table_name query: INSERT INTO $table_name VALUES($query) | " . mysqli_error($con));
    
    $_SESSION["database_message"] = "A new record has been added!";
    $_SESSION["database_message_type"] = "success";
        
    header("Location: ../index.php?mn=" . $mn . "&cn=" . $cn);
    exit();
}

/* Processing Table: EDIT */
//Check to see if the edit button has been pressed.
elseif(isset($_GET["edit"]))
{
    $columns = mysqli_query($con, "SHOW COLUMNS FROM $table_name");
    while($result = mysqli_fetch_array($columns))
    {
        $identifier[] = $result["0"];
    }
    unset($result);
    
    $value = explode(",", filter_input(INPUT_GET, "edit")); 
    $query = implode(" AND ", array_map(function($e1, $e2){ 
        if(strcasecmp($e2, "null") == 0):
            return $e1 . " is NULL"; 
        else:
            return $e1 . "=\"" . $e2 ."\""; 
        endif;
    }, $identifier, $value));
    
    $output = mysqli_query($con, "SELECT * FROM $table_name WHERE $query")
    or die("Error on $table_name query: SELECT * FROM $table_name WHERE $query | " . mysqli_error($con));
    $data2dArr = array();
    while($result = mysqli_fetch_array($output, MYSQLI_ASSOC))
    {
        $i = 0;
        foreach($result as $col_value)
        {
            $data2dArr[$i][] = $col_value;
            $i++;
        }
    }
    
    $_SESSION["is_edit"] = true;
    $_SESSION["row-edit"] = intval(filter_input(INPUT_GET, "row-edit")); 
    $_SESSION["data_output"] = implode(",", array_map(function($e){ return $e[0]; }, $data2dArr));

    if(isset($_GET["desc"]))
    {
        header("Location: ../index.php?mn=" . $mn . "&cn=" . $cn . "&desc#editor");
    }
    else 
    {
        header("Location: ../index.php?mn=" . $mn . "&cn=" . $cn . "#editor");
    }
    exit();
}

/* Processing Table: UPDATE */
//Check to see if the update button has been pressed.
elseif(isset($_POST["update"]))
{
    $columns = mysqli_query($con, "SHOW COLUMNS FROM $table_name");
    while($result = mysqli_fetch_array($columns))
    {
        $userInput[] = filter_input(INPUT_POST, $result["0"]);
        $oldData[] = filter_input(INPUT_POST, "old_" . $result["0"]);
        $identifier[] = $result["0"];
    }
    unset($result);
    
    $queryA = implode(", ", array_map(function($e1, $e2){ return $e1 . "=\"" . $e2 ."\""; }, $identifier, $userInput));
    $queryB = implode(" AND ", array_map(function($e1, $e2){ return $e1 . "=\"" . $e2 ."\""; }, $identifier, $oldData));
    
    mysqli_query($con, "UPDATE $table_name SET $queryA WHERE $queryB")
        or die("Error on $table_name query: UPDATE $table_name SET $queryA WHERE $queryB | " . mysqli_error($con));
        
    $_SESSION["database_message"] = "The record has been updated!";
    $_SESSION["database_message_type"] = "success";   
    
    header("Location: ../index.php?mn=" . $mn . "&cn=" . $cn);
    exit();
}

/* Processing Table: DELETE */
//Check to see if the delete button has been pressed.
elseif(isset($_GET["delete"]))
{
    $value = explode(",", $_GET["delete"]); 
    $columns = mysqli_query($con, "SHOW COLUMNS FROM $table_name");
    while($result = mysqli_fetch_array($columns))
    {
        $identifier[] = $result["0"];
    }
    
    $query = implode(" AND ", array_map(function($e1, $e2){ return $e1 . "=\"" . $e2 ."\""; }, $identifier, $value));
    mysqli_query($con, "DELETE FROM $table_name WHERE $query")
        or die("Error on $table_name query: DELETE FROM $table_name WHERE $query | " . mysqli_error($con));
    
    $_SESSION["database_message"] = "The record has been deleted!";
    $_SESSION["database_message_type"] = "success";
    
    header("Location: ../index.php?mn=" . $mn . "&cn=" . $cn);
    exit();
}

/* Nothing to process... */
else 
{
    header("Location: ../index.php?redirect");
    exit();
}
?>
