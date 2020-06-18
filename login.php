<?php
include('lib/common.php');
/////////////////
if ($showQueries) {
    array_push($query_msg, "showQueries currently turned ON, to disable change to 'false' in lib/common.php");
}

////////////////////////
//Note: known issue with _POST always empty using PHPStorm built-in web server: Use *AMP server instead
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    ////////////mysqli_real_escape_string
    $enteredUsername = mysqli_real_escape_string($db, $_POST['username']);
    $enteredPassword = mysqli_real_escape_string($db, $_POST['password']);

    if (empty($enteredUsername)) {
        array_push($error_msg,  "Please enter your Username.");
    }

    if (empty($enteredPassword)) {
        array_push($error_msg,  "Please enter a password.");
    }

    if (!empty($enteredUsername) && !empty($enteredPassword)) {

        $query = "SELECT Password FROM `user` WHERE Username='$enteredUsername'";

        $result = mysqli_query($db, $query);
        include('lib/show_queries.php');
        $count = mysqli_num_rows($result);

        if (!empty($result) && ($count > 0)) {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $storedPassword = $row['Password'];

            $options = [
                'cost' => 8,
            ];
            //convert the plaintext passwords to their respective hashses
            $storedHash = password_hash($storedPassword, PASSWORD_DEFAULT, $options);   //may not want this if $storedPassword are stored as hashes (don't rehash a hash)
            $enteredHash = password_hash($enteredPassword, PASSWORD_DEFAULT, $options);

            if ($showQueries) {
                array_push($query_msg, "Plaintext entered password: " . $enteredPassword);
                //Note: because of salt, the entered and stored password hashes will appear different each time   
                array_push($query_msg, "Entered Hash:" . $enteredHash);
                array_push($query_msg, "Stored Hash:  " . $storedHash);  //note: change to storedHash if tables store the plaintext password value
                //unsafe, but left as a learning tool uncomment if you want to log passwords with hash values
                //error_log('email: '. $enteredEmail  . ' password: '. $enteredPassword . ' hash:'. $enteredHash);
            }

            //depends on if you are storing the hash $storedHash or plaintext $storedPassword 
            if (password_verify($enteredPassword, $storedHash)) {
                array_push($query_msg, "Password is Valid! ");
                
                $_SESSION['username'] = $enteredUsername;
                /////////////////////////////////////
                //need a query for session variable userrole volunteer 
                /////////////////////////////////////
                array_push($query_msg, "logging in... ");
                header(REFRESH_TIME . 'url=dashboard.php');        //to view the password hashes and login success/failure

            } else {
                array_push($error_msg, "Login failed: " . $enteredUsername . NEWLINE);
                array_push($error_msg, "To demo enter: " . NEWLINE . "inge" . NEWLINE . "inge");
            }
        } else {
            array_push($error_msg, "The username entered does not exist: " . $enteredUsername);
        }
    }
}
?>

<?php include("lib/header.php"); ?>
<title>Login</title>
</head>

<body>
    <div id="main_container">
        <div class="center_content">
            <div class="text_box">

                <form action="login.php" method="post" enctype="multipart/form-data">
                    <div class="title">Inge's Animal Haven</div>
                    <div class="login_form_row">
                        <label class="login_label">Username:</label>
                        <input type="text" name="username" value="inge" class="login_input" />
                    </div>
                    <div class="login_form_row">
                        <label class="login_label">Password:</label>
                        <input type="password" name="password" value="inge" class="login_input" />
                    </div>
                    <input type="image" src="img/login.gif" class="login" />
                </form>
            </div>

            <?php include("lib/error.php"); ?>

            <div class="clear"></div>
        </div>

        <?php include("lib/footer.php"); ?>

    </div>
</body>

</html>