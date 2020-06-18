<?php

include('lib/common.php');
// written by GTusername4

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $month = mysqli_real_escape_string($db, $_POST['month']);
    $month = str_replace("-","",$month);

    $query = "SELECT USER.Username, user.FirstName, user.LastName, user.Email, TotalHour " . 
            "FROM( " . 
            "SELECT Username, SUM(workhour) as TotalHour " . 
            "FROM hoursworked " . 
            "WHERE EXTRACT( YEAR_MONTH FROM Date ) = '$month' " . 
            "GROUP BY Username " . 
            "ORDER BY TotalHour DESC " . 
            "LIMIT 5 " . 
            ") topvolunteer " .
            "LEFT JOIN User " .
            "ON topvolunteer.Username = USER.Username;";

        // SELECT USER.Username, user.FirstName, user.LastName, user.Email, TotalHour
        // FROM(
        //     SELECT Username, SUM(workhour) as TotalHour
        //     FROM hoursworked 
        //     WHERE EXTRACT( YEAR_MONTH FROM Date ) = '$month' 
        //     GROUP BY Username
        //     ORDER BY TotalHour DESC
        //     LIMIT 5
        //     ) topvolunteer
        // LEFT JOIN User
        // ON topvolunteer.Username = USER.Username;


    $result = mysqli_query($db, $query);
    include('lib/show_queries.php');

    if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg,  "DELETE ERROR: volunteer...<br>" . __FILE__ . " line:" . __LINE__);
    }
}

?>

<?php include("lib/header.php"); ?>

<title>Animal Control Report</title>
</head>

<body>
    <div id="main_container">
        <?php include("lib/menu.php"); ?>

        <div class="center_content">
            <div class="center_left">
                <div class="title_name">Volunteer of the Month</div>
                <div class="features">
                    <div class="section">
                        <form name="filtermonth" action="monthly_volunteer.php" method="post">
                            <label for="month">Select (month and year):</label>
                            <input type="month" name="month">
                            <a href="javascript:filtermonth.submit();" class="fancy_button">Go</a>
                        </form>
                    </div>

                    <div class="section">
                        <table>
                            <?php
                            if (isset($result)) {
                                print "<tr>";
                                print "<td class='heading'>First Name</td>";
                                print "<td class='heading'>Last Name</td>";
                                print "<td class='heading'>Email</td>";
                                print "<td class='heading'>Total Hours</td>";
                                print "</tr>";                               
                                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                                    print "<tr>";
                                    print "<td>{$row['FirstName']}</td>";
                                    print "<td>{$row['LastName']}</td>";
                                    print "<td>{$row['Email']}</td>";
                                    print "<td>{$row['TotalHour']}</td>";
                                    print "</tr>";
                                }
                            }    ?>
                        </table>
                    </div>                    
                </div>
            </div>
            <?php include("lib/error.php"); ?>

            <div class="clear"></div>
        </div>
        <?php include("lib/footer.php"); ?>
    </div>
</body>

</html>