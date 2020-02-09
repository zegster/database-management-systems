<?php
/* PHP 7.4.2 */
/* Start Session */
session_start();


/* Database connection */
$con = (require_once "./php/connection.php");


/* Menu Number and Column Number */
$mn = intval(filter_input(INPUT_GET, "mn"));
$cn = intval(filter_input(INPUT_GET, "cn"));


/* Store table names in an array */
/* Make table headers */
//Iterate through the rows of the result table and sorted it by descending order
$table = mysqli_query($con, "SHOW TABLES ");
while($result = mysqli_fetch_array($table))
{
    $tableArray[] = $result["0"];
    $headerArray[] = ucwords(str_replace("_", " ", $result["0"]));
}
rsort($tableArray);
rsort($headerArray);
$table_name = $tableArray[$mn];


/* Retrieve all the columns for the current table */
//Iterate through the rows of the result table
$column = mysqli_query($con, "SHOW COLUMNS FROM $table_name");
while($result = mysqli_fetch_array($column))
{
	$displayFields[] = ucwords(str_replace("_", " ", $result["0"]));
	$fields[] = $result["0"];
}


/* Sorting based on the selected column and by keyword search (if any)
 * NOTE: For descending order, use the keyword DESC. */
$isSearchMode = false;
if(isset($_POST["keyword_search"]))
{
	$isSearchMode = true;
	$data2dArr = array();
	$toSearch = str_replace(" ", "|", filter_input(INPUT_POST, "keyword_search"));
	$toSearch = " REGEXP \"" . $toSearch . "\"";
	
	$searchQuery = implode($toSearch . " OR ", $fields) . $toSearch;
	if(isset($_GET["desc"]))
	{
		$sortedResult = mysqli_query($con, "SELECT * FROM $table_name WHERE $searchQuery ORDER BY $fields[$cn] DESC");
	}
	else
	{
		$sortedResult = mysqli_query($con, "SELECT * FROM $table_name WHERE $searchQuery ORDER BY $fields[$cn]");
	}
}
else
{
	$data2dArr = array();
	if(isset($_GET["desc"]))
	{
		$sortedResult = mysqli_query($con, "SELECT * FROM $table_name ORDER BY $fields[$cn] DESC");
	}
	else
	{
		$sortedResult = mysqli_query($con, "SELECT * FROM $table_name ORDER BY $fields[$cn]");
	}
}


/* Getting column data */
$isDataEmpty = false;
if(mysqli_num_rows($sortedResult) > 0)
{
    while($line = mysqli_fetch_array($sortedResult, MYSQLI_ASSOC))
    {
        $i = 0;
        foreach($line as $col_value)
        {
            $data2dArr[$i][] = $col_value;  //ROW x COLUMN
            $i++;
        }
    }
}
else
{
    $isDataEmpty = true;
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
		<link rel="stylesheet" type="text/css" href="css/main.css" />
		
		<!-- JS -->
		<script type="text/javascript" src="js/university.js"></script>
		
		<title>University Database Managment</title>
	</head>

	<body id="main-background" class="dimmable">
		<!-- No JavaScript Error Message -->
		<div id="javascript-warning" class="ui active dimmer">
			<div class="ui text loader"><i class="fa fa-exclamation-triangle"></i>&emsp;Error: Please enable JavaScript...</div>
		</div>

		<!-- Database Message -->
		<?php if(isset($_SESSION["database_message"])): ?>
		<div class="ui <?php echo $_SESSION["database_message_type"]; ?> no-margin message">
			<i class="close icon"></i>
			<div class="header">
				<?php echo ucwords($_SESSION["database_message_type"]); ?>
			</div>
			<p><?php echo $_SESSION["database_message"]; ?></p>
		</div>
		<?php endif; ?>
	
		<!-- Navigation Menu -->
		<div class="ui container">
			<div class="ui borderless stackable no-bottom-border-radius no-margin inverted menu">
				<!-- Keyword Search -->
				<div class="item">
					<form class="max-width" action="index.php?mn=<?php echo $mn?>&cn=<?php echo $cn?>" method="POST">
						<div class="ui action max-width input">
							<input class="search-bar" type="text" name="keyword_search" placeholder="Search by keyword...">
							<button class="ui teal icon button" type="submit">
								<i class="fa fa-search"></i>
							</button>
						</div>
					</form>	
				</div>
				
				<!-- Refresh/Admin Button -->
				<div class="right menu">
					<div class="item">
						<div class="ui buttons">
							<button class="ui teal button" onclick="window.location.href = 'index.php?mn=<?php echo $mn?>&cn=<?php echo $cn?>'">
								<i class="fa fa-refresh"></i>&emsp;Refresh
							</button>
							<button class="ui teal button" onclick="window.location.href = './php/admin.php'">
								<i class="fa fa-user"></i>&emsp;Admin
							</button>
						</div>
					</div>
				</div>	
			</div>
        	
			
			<!-- Table Menu -->
			<div class="ui borderless stackable no-top-border-radius no-margin inverted pointing teal menu">
    			<?php for($i = 0; $i < count($headerArray); $i++): ?>
                    <?php if($mn == $i): ?>
                        <a class="active item"><?php echo $headerArray[$i]; ?></a>
                    <?php else: ?>
                        <a class="item" href="index.php?mn=<?php echo $i; ?>"><?php echo $headerArray[$i]; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
			</div>
		</div>
	
		<div class="ui hidden divider"></div>
	
		<!-- Database Information -->
		<div class="ui container">
			<table class="ui selectable fixed inverted striped table">
				<!-- Column Name -->
				<thead>
					<!-- Column Name: Label -->
					<tr class="center aligned">
						<?php for($i = 0; $i < count($displayFields); $i++): ?>
                        	<th>
                        		<strong><?php echo $displayFields[$i]; ?></strong>&emsp;
                    		</th>
                        <?php endfor; ?>
                        <th></th>
					</tr>
					<!-- Column Name: Sorting Ascending/Descending -->
					<tr class="center aligned">
						<?php for($i = 0; $i < count($displayFields); $i++): ?>
                        	<th>
                        		<div class="ui buttons">
                            		<button class="ui compact teal icon button" onclick="sortCurrentField(1, <?php echo $mn; ?>, <?php echo $i; ?>)">
                                    	<i class="fa fa-sort-amount-asc"></i>
                                    </button>
                                    <button class="ui compact teal icon button" onclick="sortCurrentField(-1, <?php echo $mn; ?>, <?php echo $i; ?>)">
                                    	<i class="fa fa-sort-amount-desc"></i>
                                    </button>
                        		</div>
                    		</th>
                        <?php endfor; ?>
                        <th></th>
					</tr>
				</thead>
				
				<!-- Display Data -->
				<tbody>
					<?php 
					if(!$isDataEmpty): 
					    for($j = 0; $j < count($data2dArr[0]); $j++): ?>
					    	<!-- Display Data: Column information -->
					    	<tr class="center aligned">
					        <?php for($k = 0; $k < count($fields); $k++): ?>
                            	<td>
                        			<?php echo $data2dArr[$k][$j]; ?>
                            	</td>
                            <?php endfor; ?>
                                <td>
                                	<?php if(isset($_SESSION["row-edit"]) && $_SESSION["row-edit"] == $j):?>
                                	<!-- Display Data: Editing notice -->
                                	<div class="ui disabled compact inverted yellow button">
                                        <i class="fa fa-cogs"></i>&emsp;Editing...
                                    </div>
                                	<?php else:?>
                                	<!-- Display Data: Edit Button and Delete Button -->
										<?php if($isSearchMode): ?>
										<button class="ui disabled compact inverted yellow button">
											<i class="fa fa-exclamation"></i>&emsp;Search Mode
										</button>
										<?php else: ?>
										<div class="ui buttons">
											<button class="ui compact green button" onclick="window.location.href = './php/process.php?mn=<?php echo $mn; ?>&cn=<?php echo $cn?><?php if(isset($_GET['desc'])): echo '&desc'; endif; ?>&edit=<?php echo implode(',', array_map(function($e) use($j) { return $e[$j]; }, $data2dArr)); ?>&row-edit=<?php echo $j?>';">
												<i class="fa fa-pencil-square-o"></i>
											</button>
											<button class="ui compact red button" onclick="window.location.href = './php/process.php?mn=<?php echo $mn; ?>&cn=<?php echo $cn?>&delete=<?php echo implode(',', array_map(function($e) use($j) { return $e[$j]; }, $data2dArr)); ?>';">
												<i class="fa fa-trash"></i>
											</button>
										</div>
										<?php endif;?>
                                	<?php endif;?>
                                </td>
                            </tr>
                    <?php endfor;
                    endif; ?>
				</tbody>
			</table>
		</div>
		
		<?php if($isDataEmpty): ?>
			<!-- Display Data: Empty result -->
			<div class="ui container">
				<div class="ui center aligned inverted secondary yellow segment">
					<i class="warning icon"></i>
					Empty results.
				</div>
			</div>
		<?php endif; ?>
		
		<div class="ui hidden divider"></div>
	
		<!-- Update/Create a row -->
		<div id="editor-toggle" class="ui container">
			<div class="ui right aligned inverted teal segment">
				<button class="ui compact blue button" type="submit" name="create">
                	<i class="fa fa-wrench"></i>&emsp;New Row
                </button>
			</div>
		</div>
		
		<div id="editor" class="ui container">
			<form id="editor-form" action="php/process.php?mn=<?php echo $mn?>&cn=<?php echo $cn?>" method="POST">
				<table class="ui fixed inverted teal table">
					<tbody>
						<tr class="center aligned">
    					<?php
    					if(isset($_SESSION["data_output"])):
    					    $dataOutput = explode(",", $_SESSION["data_output"]);
    					endif;
					
    					for($i = 0; $i < count($fields); $i++): ?>
    						<!-- Update/Create a row: Input field -->
                        	<td>
                        		<div class="ui mini input">
                        			<input type="text" name="<?php echo $fields[$i]; ?>" value="<?php if(isset($_SESSION["data_output"])): echo $dataOutput[$i]; endif; ?>" placeholder="<?php echo ucwords(str_replace("_", " ", $fields[$i])); ?>">
                                    <input type="hidden" name="old_<?php echo $fields[$i]; ?>" value="<?php if(isset($_SESSION["data_output"])): echo $dataOutput[$i]; endif; ?>">
                    			</div>
                    		</td>
                        <?php endfor; ?>
                        	<!-- Update/Create a row: Submit Button for input field -->
                            <td>
                            	<div>
                            	<?php if(isset($_SESSION["is_edit"]) && $_SESSION["is_edit"]): ?>
                                    <button class="ui compact blue button" type="submit" name="update">
                                    	<i class="fa fa-pencil-square-o"></i>&emsp;UPDATE
                                    </button>
                                <?php else: ?>
                                	<button class="ui compact blue button" type="submit" name="create">
                                    	<i class="fa fa-plus"></i>&emsp;CREATE
                                    </button>
                                <?php endif; ?>
        						</div>
                            </td>
    					</tr>
					</tbody>
				</table>
			</form>
		</div>


	</body>
</html>

<?php
/* Remove specific session */
unset($_SESSION["database_message"]);
unset($_SESSION["database_message_type"]);
unset($_SESSION["is_edit"]);
unset($_SESSION["row-edit"]);
unset($_SESSION["data_output"]);
?>
