<?php
/* PHP 7.4.2 */
/* Start Session */
session_start();


/* Redirect back to login page if login information doesn't exist */ 
if(!isset($_SESSION['user_id']))
{
    header("Location: ./login.php?redirect");
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


/* Processing Table: ALTER */
if(isset($_POST["invoke_alter"]))
{
    $selectedTable = filter_input(INPUT_POST, "database_table");//required
    $columnName = filter_input(INPUT_POST, "column_name");//required
    $columnType = filter_input(INPUT_POST, "column_type");//required
    $columnTypeLength = filter_input(INPUT_POST, "column_type_length");//required
    $columnDefault = filter_input(INPUT_POST, "column_default");
    $columnAsDefinedValue = filter_input(INPUT_POST, "column_as_defined_value");
    $columnNull = filter_input(INPUT_POST, "column_null");
    $columnAttributes = filter_input(INPUT_POST, "column_attributes");

    // echo "tst: " . 
    // $selectedTable . $columnName . $columnType . $columnTypeLength . 
    // $columnDefault . $columnAsDefinedValue . $columnNull . $columnAttributes;

    //TODO create a sql to create a table: ALTER TABLE `student` ADD `Test` INT(1) UNSIGNED NULL DEFAULT NULL
    if(empty($selectedTable) || empty($columnName) || empty($columnType) || empty($columnTypeLength))
    {
        $_SESSION["database_message"] = "Required field can not be empty!";
        $_SESSION["database_message_type"] = "negative";
    }
    else
    {

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

        <!-- Adding Column Form -->
        <div class="ui container">
            <div class="ui centered grid">
                <div class="column">
                    <div class="ui inverted teal segment">
                        <div class="ui huge center aligned header">
                            <i class="fa fa-server"></i>&emsp;Structure Manipulation
                        </div>
                    </div>

                    <!-- Structure Manipulation -->
                    <form action="./admin.php" method="POST">
                        <div class="ui equal width form">
                            <div class="ui segment">
                                <!-- Database Message -->
                                <?php if(isset($_SESSION['database_message'])): ?>
                                <div class="ui <?php echo $_SESSION['database_message_type']; ?> message">
                                    <i class="close icon"></i>
                                    <div class="header">
				                        <?php echo ucwords($_SESSION["database_message_type"]); ?>
			                        </div>
                                    <p><?php echo $_SESSION['database_message']; ?></p>
                                </div>
                                <?php endif; ?>

                                <!-- Structure Manipulation: Database Table -->
                                <div class="required field">
                                    <label>Database Table</label>
                                    <select class="ui dropdown" name="database_table">
                                        <option value="" disabled selected>Please Choose...</option>
                                        <?php
                                            $table = mysqli_query($con, "SHOW TABLES ");
                                            while($result = mysqli_fetch_array($table))
                                            {
                                                $tableArray[] = $result["0"];
                                                $tablenameArray[] = ucwords(str_replace("_", " ", $result["0"]));
                                            }
                                        ?>
                                        <?php for($i = 0; $i < count($tableArray); $i++): ?>
                                            <option value="<?php echo $tableArray[$i] ?>"><?php echo $tablenameArray[$i] ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <!-- Structure Manipulation: Column Name -->
                                <div class="required field">
                                    <label>Column Name</label>
                                    <input type="text" name="column_name" placeholder="Column Name">
                                </div>

                                <!-- Structure Manipulation: Column Type and Length -->
                                <div class="equal width fields">
                                    <div class="required field">
                                        <label>Column Type</label>
                                        <select class="ui dropdown" name="column_type">
                                            <option value="" disabled selected>Please Choose...</option>
                                            <option value="int">INT</option>
                                            <option value="varchar">VARCHAR</option>
                                            <option value="text">TEXT</option>
                                            <option value="date">DATE</option>
                                        </select>
                                    </div>
                                    <div class="required field">
                                        <label>Column Type Length</label>
                                        <input type="text" name="column_type_length" placeholder="Column Type Length">
                                    </div>
                                </div>

                                <!-- Structure Manipulation: Column Default -->
                                <div class="equal width fields">
                                    <div class="field">
                                        <label>Column Default</label>
                                        <select class="ui dropdown" id="as-defined" name="column_default">
                                            <option value="">None</option>
                                            <option value="as defined">As Defined</option>
                                            <option value="curent_timestamp">CURRENT_TIMESTAMP</option>
                                        </select>
                                    </div>
                                    <div class="required field" id="as-defined-value">
                                        <label>As Defined Value</label>
                                        <input type="text" name="column_as_defined_value" placeholder="Column As Defined Value">
                                    </div>
                                </div>

                                <!-- Structure Manipulation: Null -->
                                <div class="field">
                                    <div class="ui toggle checkbox">
                                        <input type="checkbox" name="column_null" value="null">
                                        <label>is Null?</label>
                                    </div>
                                </div>

                                <!-- Structure Manipulation: Attributes -->
                                <div class="field">
                                    <label>Column Attributes</label>
                                    <select class="ui dropdown" name="column_attributes">
                                        <option value=""></option>
                                        <option value="BINARY">BINARY</option>
                                        <option value="UNSIGNED">UNSIGNED</option>
                                        <option value="UNSIGNED ZEROFILL">UNSIGNED ZEROFILL</option>
                                        <option value="on update CURRENT_TIMESTAMP">on update CURRENT_TIMESTAMP</option>
                                    </select>
                                </div>

                                <!-- Create Button -->
                                <div class="field">
                                    <button class="fluid ui blue button" type="submit" name="invoke_alter">
                                        <i class="fa fa-plus"></i>&emsp;Create
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
