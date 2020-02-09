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
		
		<title>University Database Managment [Administration]</title>
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
                        </div>&emsp;
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
    </body>
</html>

<?php
/* Remove specific session */
unset($_SESSION['database_message']);
unset($_SESSION['database_message_type']);
?>
