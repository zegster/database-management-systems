<?php
/* PHP 7.4.2 */
/* Start Session */
session_start();

/* Redirect back to login page if login information doesn't exist */ 
if(!isset($_SESSION['user_id']))
{
    header("Location: ./login.php?redirect");
    exit();
}

/* Sign out admin mode */
//Check to see if the sign out button has been pressed.
if(isset($_POST["sign_out"]))
{
    unset($_SESSION["user_id"]);
    $_SESSION["database_message"] = "Signed out successfully!";
    $_SESSION["database_message_type"] = "success";
        
    header("Location: ../index.php");
    exit();
}

/* Database connection */
$con = (require_once "./connection.php");

/* Get all table in the selected database */
$tables = mysqli_query($con, "SHOW TABLES ");
while($result = mysqli_fetch_array($tables))
{
    $tableArray[] = $result["0"];
    $tablenameArray[] = ucwords(str_replace("_", " ", $result["0"]));
}
unset($result);

/* Retrieve all the columns for all the table */
for($i = 0; $i < count($tableArray); $i++)
{
    $columns = mysqli_query($con, "SHOW COLUMNS FROM " . $tableArray[$i]);
    while($result = mysqli_fetch_array($columns))
    {
        $tableColumnArray[$i][] = $result["0"];
        $columnArray[$tableArray[$i]][] = $result["0"];
    }
}
unset($result);


/* Processing Table: ALTER ADD */
if(isset($_POST["invoke_alter_add"]))
{
    $selectedTable = strtolower(filter_input(INPUT_POST, "database_table"));//required
    $columnName = strtolower(filter_input(INPUT_POST, "column_name"));//required
    $columnType = strtolower(filter_input(INPUT_POST, "column_type"));//required
    $columnTypeLength = filter_input(INPUT_POST, "column_type_length");//required
    $columnDefault = strtolower(filter_input(INPUT_POST, "column_default"));
    $columnAsDefinedValue = strtolower(filter_input(INPUT_POST, "column_as_defined_value"));
    $columnNull = strtolower(filter_input(INPUT_POST, "column_null"));
    $columnAttributes = strtolower(filter_input(INPUT_POST, "column_attributes"));

    if(empty($selectedTable) || empty($columnName) || empty($columnType))
    {
        $_SESSION["database_message"] = "Required field can not be empty!";
        $_SESSION["database_message_type"] = "error";
    }
    else
    {
        /* Error Flag */
        $isValidationError = false;
        $query = "ALTER TABLE ";

        /* Check to see if the input for the selected table is exist */
        if(in_array($selectedTable, $tableArray)):
            $query .= "" . $selectedTable . " ADD " . $columnName . " ";
        else:
            $_SESSION["database_message"] = "Selected table does not exist!";
            $_SESSION["database_message_type"] = "error";
            $isValidationError = true;
        endif;

        /* Check to see if the input for column type and length is valid */
        if(in_array($columnType, array("int", "varchar", "text", "date"))):
            if(in_array($columnType, array("text", "date"))):
                $query .= strtoupper($columnType) . " ";
            else:
                if(!empty($columnTypeLength) && ctype_digit($columnTypeLength)):
                    $query .= strtoupper($columnType) . "(" . $columnTypeLength . ") ";
                else:
                    $_SESSION["database_message"] = "Column length cannot be empty for this type and must be whole number!";
                    $_SESSION["database_message_type"] = "error";
                    $isValidationError = true;
                endif;
            endif;
        else:
            $_SESSION["database_message"] = "Selected column type does not exist!";
            $_SESSION["database_message_type"] = "error";
            $isValidationError = true;
        endif;

        /* Check to see if the input for attributes is not empty and is exist */
        if(!empty($columnAttributes)):
            if(in_array($columnAttributes, array("binary", "unsigned", "unsigned zerofill", "on update current_timestamp"))):
                $query .=  strtoupper($columnAttributes) . " ";
            else:
                $_SESSION["database_message"] = "Selected column attributes does not exist!";
                $_SESSION["database_message_type"] = "error";
                $isValidationError = true;
            endif;
        endif;

        /* Check to see if the input is null */
        if(!empty($columnNull)):
            $query .=  "NULL ";
        else:
            $query .=  "NOT NULL ";
        endif;

        /* Check to see if the input for default is not empty and is exist */
        if(!empty($columnDefault)):
            if(in_array($columnDefault, array("as defined", "curent_timestamp"))):
                if(strcasecmp($columnDefault, "as defined") == 0):
                    $query .= "DEFAULT \"" . $columnAsDefinedValue . "\" ";
                else:
                    $query .= "DEFAULT CURRENT_TIMESTAMP ";
                endif;
            else:
                $_SESSION["database_message"] = "Selected column default does not exist!";
                $_SESSION["database_message_type"] = "error";
                $isValidationError = true;
            endif;
        endif;

        /* Execute SQL query when there is no error */
        if(!$isValidationError)
        {
            mysqli_query($con, $query) or die("Error on $selectedTable query: $query | " . mysqli_error($con));
            $_SESSION["database_message"] = "A new column has been added!";
            $_SESSION["database_message_type"] = "success";

            header("Location: ./admin.php?redirect");
            exit();
        }
    }
}


/* Processing Table: ALTER DROP */
else if(isset($_POST["invoke_alter_drop"]))
{
    $selectedTable = strtolower(filter_input(INPUT_POST, "database_table"));//required
    $selectedTableColumn = strtolower(filter_input(INPUT_POST, "database_table_column"));//required

    if(empty($selectedTable) || empty($selectedTableColumn))
    {
        $_SESSION["database_message"] = "Required field can not be empty!";
        $_SESSION["database_message_type"] = "error";
    }
    else
    {
        /* Error Flag */
        $isValidationError = false;

        /* Check to see if the input for the selected table is exist */
        if(!in_array($selectedTable, $tableArray)):
            $_SESSION["database_message"] = "Selected table does not exist!";
            $_SESSION["database_message_type"] = "error";
            $isValidationError = true;
        endif;

        /* Check to see if the input for the selected table column is exist */
        if(!in_array($selectedTableColumn, $columnArray[$selectedTable])):
            $_SESSION["database_message"] = "Selected column does not exist!";
            $_SESSION["database_message_type"] = "error";
            $isValidationError = true;
        endif;
        
        /* Execute SQL query when there is no error */
        if(!$isValidationError)
        {
            mysqli_query($con, "ALTER TABLE $selectedTable DROP COLUMN $selectedTableColumn") 
                or die("Error on $selectedTable query: ALTER TABLE Customers DROP COLUMN ContactName | " . mysqli_error($con));
                
            $_SESSION["database_message"] = "The selected column has been deleted!";
            $_SESSION["database_message_type"] = "success";

            header("Location: ./admin.php?redirect");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- jQuery library -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

		<!-- Semantic UI -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css"/>
    	<script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js"></script>
		
		<!-- Latest compiled JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
		
		<!-- Font Awesome 4 Icons -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		
		<!-- Custom CSS -->
		<link rel="stylesheet" type="text/css" href="../css/main.css" />
		
		<!-- JS -->
		<script type="text/javascript" src="../js/university.js"></script>
        <script type="text/javascript" src="../js/admin.js"></script>
		
		<title>University Database Managment [Administration]</title>
	</head>

	<body id="main-background" class="dimmable">
		<!-- No JavaScript Error Message -->
		<div id="javascript-warning" class="ui active dimmer">
			<div class="ui text loader"><i class="fa fa-exclamation-triangle"></i>&emsp;Error: Please enable JavaScript...</div>
		</div>

        <!-- Database Message -->
        <?php if(isset($_SESSION['database_message'])): ?>
            <div class="ui <?php echo $_SESSION['database_message_type'] ?> no-margin message">
                <i class="close icon"></i>
                <div class="header">
                    <?php echo (strcasecmp($_SESSION["database_message_type"], "negative") == 0) ? "Error" : ucwords($_SESSION["database_message_type"]) ?>
                </div>
                <p><?php echo $_SESSION['database_message'] ?></p>
            </div>
        <?php endif; ?>

        <!-- Navigation Menu -->
		<div class="ui container">
            <div class="ui borderless stackable no-bottom-border-radius no-margin inverted menu">
                <!-- Home/Refresh/Sign Out Button -->
                <div class="right menu">
                    <div class="item">
                        <div class="ui buttons">
                            <button class="ui teal button" onclick="window.location.href = '../index.php'">
                                <i class="fa fa-home"></i>&emsp;Home
                            </button>
                            <button class="ui teal button" onclick="window.location.href = './admin.php'">
                                <i class="fa fa-refresh"></i>&emsp;Refresh
                            </button>
                        </div>
                    </div>
                    <div class="item">
                        <form action="./admin.php" method="POST">
                            <button class="ui teal button" type="submit" name="sign_out">
                                <i class="fa fa-sign-out"></i>&emsp;Sign Out
                            </button>
                        </form>
                    </div>
                </div>	
            </div>
        </div>

        <div class="ui hidden divider"></div>
        
        <!-- Structure Manipulation Create/Delete -->
        <div class="ui container">
            <div class="ui inverted teal segment no-bottom-border-radius">
                <div class="ui huge center aligned header">
                    <i class="fa fa-server"></i>&emsp;Structure Manipulation
                </div>
            </div>
        </div>
        <div class="ui container">
            <div class="ui inverted form">
                <div class="ui inverted blue segment no-top-border-radius">
                    <div class="field">
                        <label>What would you like to do?</label>
                        <select id="form-structure-toggler" class="ui dropdown" name="form_structure">
                            <option value="" disabled selected>Please Choose...</option>
                            <option value="create_column">Create Column</option>
                            <option value="delete_column">Delete Column</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="ui hidden divider"></div>

        <!-- Structure Manipulation -->
        <div class="ui container">
            <div class="ui centered grid">
                <div class="column">
                    <!-- Add Column Form -->
                    <form id="form-structure-create" action="./admin.php" method="POST">
                        <div class="ui equal width form">
                            <div class="ui segment">
                                <!-- Add Column Form: Database Table -->
                                <div class="required field">
                                    <label>Database Table</label>
                                    <select class="ui dropdown" id="database-table-add" name="database_table">
                                        <option value="" disabled selected>Please Choose...</option>
                                        <?php for($i = 0; $i < count($tableArray); $i++): ?>
                                            <option value="<?php echo $tableArray[$i] ?>"><?php echo $tablenameArray[$i] ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <!-- Add Column Form: Preview Existing Column-->
                                <?php for($i = 0; $i < count($tableArray); $i++): ?>
                                <div class="field" id="structure-current-<?php echo $tableArray[$i] ?>">
                                    <label>Current <?php echo ucwords(str_replace("_", " ", $tableArray[$i])) ?> Column</label>
                                    <?php for($j = 0; $j < count($tableColumnArray[$i]); $j++): ?>
                                        <div class="ui teal label cushioned">
                                            <?php echo ucwords(str_replace("_", " ", $tableColumnArray[$i][$j])) ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                <?php endfor; ?>

                                <!-- Add Column Form: Column Name -->
                                <div class="required field">
                                    <label>Column Name</label>
                                    <input type="text" name="column_name" placeholder="Column Name">
                                </div>

                                <!-- Add Column Form: Column Type and Length -->
                                <div class="equal width fields">
                                    <div class="required field">
                                        <label>Column Type</label>
                                        <select class="ui dropdown" id="column-type" name="column_type">
                                            <option value="" disabled selected>Please Choose...</option>
                                            <option value="int">INT</option>
                                            <option value="varchar">VARCHAR</option>
                                            <option value="text">TEXT</option>
                                            <option value="date">DATE</option>
                                        </select>
                                    </div>
                                    <div class="required field" id="column-type-length">
                                        <label>Column Type Length</label>
                                        <input type="text" name="column_type_length" placeholder="Column Type Length">
                                    </div>
                                </div>

                                <!-- Add Column Form: Column Default -->
                                <div class="equal width fields">
                                    <div class="field">
                                        <label>Column Default</label>
                                        <select class="ui dropdown" id="column-default" name="column_default">
                                            <option value="">None</option>
                                            <option value="as defined">As Defined</option>
                                            <option value="curent_timestamp">CURRENT_TIMESTAMP</option>
                                        </select>
                                    </div>
                                    <div class="required field" id="column-default-value">
                                        <label>As Defined Value</label>
                                        <input type="text" name="column_as_defined_value" placeholder="Column As Defined Value">
                                    </div>
                                </div>

                                <!-- Add Column Form: Null -->
                                <div class="field">
                                    <div class="ui toggle checkbox">
                                        <input type="checkbox" name="column_null" value="null">
                                        <label>is Null?</label>
                                    </div>
                                </div>

                                <!-- Add Column Form: Attributes -->
                                <div class="field">
                                    <label>Column Attributes</label>
                                    <select class="ui dropdown" name="column_attributes">
                                        <option value=""></option>
                                        <option value="binary">BINARY</option>
                                        <option value="unsigned">UNSIGNED</option>
                                        <option value="unsigned zerofill">UNSIGNED ZEROFILL</option>
                                        <option value="on update current_timestamp">on update CURRENT_TIMESTAMP</option>
                                    </select>
                                </div>

                                <!-- Add Column Form: Create Button -->
                                <div class="field">
                                    <button class="fluid ui blue button" type="submit" name="invoke_alter_add">
                                        <i class="fa fa-plus"></i>&emsp;Create
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Drop Column Form -->
                    <form id="form-structure-delete" action="./admin.php" method="POST">
                        <div class="ui equal width form">
                            <div class="ui segment">
                                <!-- Drop Column Form: Database Table -->
                                <div class="required field">
                                    <label>Database Table</label>
                                    <select class="ui dropdown" id="database-table-drop" name="database_table">
                                        <option value="" disabled selected>Please Choose...</option>
                                        <?php for($i = 0; $i < count($tableArray); $i++): ?>
                                            <option value="<?php echo $tableArray[$i] ?>"><?php echo $tablenameArray[$i] ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                
                                <!-- Drop Column Form: Database Table Column-->
                                <?php for($i = 0; $i < count($tableArray); $i++): ?>
                                <div class="required field" id="structure-delete-<?php echo $tableArray[$i] ?>">
                                    <label><?php echo ucwords(str_replace("_", " ", $tableArray[$i])) ?> Column</label>
                                    <select class="ui dropdown" name="database_table_column">
                                        <option value="" disabled selected>Please Choose...</option>
                                        <?php for($j = 0; $j < count($tableColumnArray[$i]); $j++): ?>
                                            <option value="<?php echo $tableColumnArray[$i][$j] ?>"><?php echo ucwords(str_replace("_", " ", $tableColumnArray[$i][$j])) ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <?php endfor; ?>

                                <!-- Drop Column Form: Delete Button -->
                                <div class="field">
                                    <button class="fluid ui red button" type="submit" name="invoke_alter_drop">
                                        <i class="fa fa-minus"></i>&emsp;Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>

<?php
/* Remove specific session */
unset($_SESSION['database_message']);
unset($_SESSION['database_message_type']);
?>
