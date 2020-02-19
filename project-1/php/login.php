<?php
/* PHP 7.4.2 */
/* Start Session */
session_start();


/* If user already signed in, redirect to a specific page */
if(isset($_SESSION["user_id"]))
{
    header("Location: ./admin.php");
    exit();
}


/* Processing Login Information */
if(isset($_POST["login"]))
{
    if(isset($_POST["username"]) && isset($_POST["password"]))
    {
        /* Database connection */
        $con = (require_once "./connection.php");

        $username = $_POST["username"];
        $password = $_POST["password"];
        $result = mysqli_query($con, "SELECT * FROM mysql.user where user=\"$username\" AND PASSWORD=\"$password\"");

        /* Check to see if login was success */
        if(mysqli_num_rows($result) > 0)
        {
            $_SESSION["user_id"] = $username;
        }
        else
        {
            $_SESSION["database_message"] = "Failed to sign in! Please try again...";
            $_SESSION["database_message_type"] = "negative";
        }

        /* If user already signed in, redirect to a specific page */
        if(isset($_SESSION["user_id"]))
        {
            header("Location: ./admin.php");
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

        <style type="text/css">
        .ui.container
        {
            height: 100%;
            width: 50%;
        }

        .ui.grid
        {
            height: 100%;
            align-items: center;
        }
        </style>

        <title>Database Managment Login</title>
    </head>

    <body id="main-background" class="dimmable">
        <!-- No JavaScript Error Message -->
        <div id="javascript-warning" class="ui active dimmer">
            <div class="ui text loader"><i class="fa fa-exclamation-triangle"></i>&emsp;Error: Please enable JavaScript...</div>
        </div>

        <!-- Login Form -->
        <div class="ui container">
            <div class="ui centered grid">
                <div class="column">
                    <div class="ui inverted teal segment">
                        <div class="ui huge center aligned header">
                            <i class="fa fa-laptop"></i>&emsp;Administration Login
                        </div>
                    </div>

                    <form action="./login.php" method="POST">
                        <div class="ui form">
                            <div class="ui segment">
                                <div class="field">
                                    <div class="ui left icon input">
                                        <input type="text" name="username" placeholder="User">
                                        <i class="user icon"></i>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="ui left icon input">
                                        <input type="password" name="password" placeholder="Password">
                                        <i class="lock icon"></i>
                                    </div>
                                </div>
                                <div class="field">
                                    <button class="fluid ui blue button" type="submit" name="login">
                                        <i class="fa fa-sign-in"></i>&emsp;Sign In
                                    </button>
                                </div>

                                <!-- Database Message -->
                                <?php if(isset($_SESSION["database_message"])): ?>
                                <div class="ui <?php echo $_SESSION["database_message_type"]; ?> no-margin message">
                                    <i class="close icon"></i>
                                    <div class="header">
                                        <?php echo (strcasecmp($_SESSION["database_message_type"], "negative") == 0) ? "Error" : ucwords($_SESSION["database_message_type"]) ?>
                                    </div>
                                    <p><?php echo $_SESSION["database_message"]; ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>

                    <div class="ui info message">
                        <div class="ui center aligned tiny header">
                            Go back to <a href="../index.php">Main Page</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

<?php
/* Remove specific session */
unset($_SESSION["database_message"]);
unset($_SESSION["database_message_type"]);
?>
