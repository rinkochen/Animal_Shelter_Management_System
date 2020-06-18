<?php

include('lib/common.php');
// written by GTusername4

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = mysqli_real_escape_string($db, $_POST['name']);

    $query = "SELECT user.FirstName, user.LastName, user.Email, volunteer.PhoneNumber " .
        "FROM volunteer INNER JOIN user " .
        "on volunteer.Username = User.Username " .
        "where LOWER(user.LastName) LIKE '%$name%' or LOWER(user.FirstName) LIKE '%$name%' ";

    // SELECT user.FirstName, user.LastName, user.Email, volunteer.PhoneNumber
    // FROM volunteer
    // INNER JOIN user
    // on volunteer.Username = User.Username
    // where user.LastName LIKE 'Jon%' or user.FirstName LIKE 'Jon%';

    $result = mysqli_query($db, $query);

    include('lib/show_queries.php');

    if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ . " line:" . __LINE__);
    }
}

?>

<?php include("lib/header.php"); ?>

<title>Volunteer Lookup</title>
</head>

<body>
    <div id="main_container">
        <?php include("lib/menu.php"); ?>

        <div class="center_content">
            <div class="center_left">
                <div class="title_name">Volunteer Lookup</div>
                <div class="features">

                    <div class="section">
                        <div class="subtitle">Search for Volunteers</div>
                        <form name="searchform" action="volunteer_lookup.php" method="POST">
                            <table>
                                <tr>
                                    <td class="item_label">Name</td>
                                    <td><input type="text" name="name" /></td>
                                </tr>
                            </table>
                            <a href="javascript:searchform.submit();" class="fancy_button">Search</a>
                        </form>
                    </div>

                    <div class="section">
                        <table>
                            <tr>
                                <th class="heading">First Name</th>
                                <th class="heading">Last Name</th>
                                <th class="heading">Email</th>
                                <th class="heading">Phone Number</th>
                            </tr>

                            <?php
                            if (isset($result)) {
                                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                                    print "<tr>";
                                    print "<td>{$row['FirstName']}</td>";
                                    print "<td>{$row['LastName']}</td>";
                                    print "<td>{$row['Email']}</td>";
                                    print "<td>{$row['PhoneNumber']}</td>";
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