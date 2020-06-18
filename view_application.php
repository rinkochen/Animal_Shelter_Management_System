<?php

include('lib/common.php');
// written by GTusername4

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}


if (!empty($_GET['approve'])) {

    $applicationnum = mysqli_real_escape_string($db, $_GET['approve']);

    $query = "UPDATE APPLICATION " .
        "SET ApplicationStatus = 'approved' " .
        "WHERE ApplicationNum = '$applicationnum' ";

    $result = mysqli_query($db, $query);
    include('lib/show_queries.php');

    if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg,  "UPDATE ERROR: approved application ... <br>" .  __FILE__ . " line:" . __LINE__);
    }
}

if (!empty($_GET['reject'])) {

    $applicationnum = mysqli_real_escape_string($db, $_GET['reject']);

    $query = "UPDATE APPLICATION " .
        "SET ApplicationStatus = 'rejected' " .
        "WHERE ApplicationNum = '$applicationnum' ";

    $result = mysqli_query($db, $query);
    include('lib/show_queries.php');

    if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg,  "UPDATE ERROR: reject application ... <br>" .  __FILE__ . " line:" . __LINE__);
    }
}

$query = "SELECT application.ApplicationNum, application.Date, application.Email, adopter.FirstName, adopter.LastName " .
    "FROM application LEFT JOIN adopter on application.Email = adopter.Email WHERE application.ApplicationStatus = 'pending'";
// SELECT application.ApplicationNum,application.Date,application.Email,adopter.FirstName,adopter.LastName
// FROM application
// LEFT JOIN adopter
// on application.Email = adopter.Email
// WHERE application.ApplicationStatus = 'pending';

$result = mysqli_query($db, $query);
include('lib/show_queries.php');

if (mysqli_affected_rows($db) == -1) {
    array_push($error_msg,  "DELETE ERROR: expiring vaccination...<br>" . __FILE__ . " line:" . __LINE__);
}




?>

<?php include("lib/header.php"); ?>

<title>Add Vaccination</title>
</head>

<body>
    <div id="main_container">
        <?php include("lib/menu.php"); ?>

        <div class="center_content">
            <div class="center_left">
                <div class="title_name">Pending Application</div>
                <div class="features">
                    <div class="section">
                        <table>
                            <tr>
                                <th class="heading">Application Number</th>
                                <th class="heading">Date</th>
                                <th class="heading">Email</th>
                                <th class="heading">First Name</th>
                                <th class="heading">Last Name</th>
                                <th class="heading">Action</th>
                            </tr>

                            <?php
                            if (isset($result)) {
                                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                                    print "<tr>";
                                    print "<td>{$row['ApplicationNum']}</td>";
                                    print "<td>{$row['Date']}</td>";
                                    print "<td>{$row['Email']}</td>";
                                    print "<td>{$row['FirstName']}</td>";
                                    print "<td>{$row['LastName']}</td>";
                                    print '<td><a href="view_application.php?approve=' . urlencode($row['ApplicationNum']) . '">Approve</a></td>';
                                    print '<td><a href="view_application.php?reject=' . urlencode($row['ApplicationNum']) . '">Reject</a></td>';
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