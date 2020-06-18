<?php

include('lib/common.php');

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$petid = $_REQUEST['petid'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = mysqli_real_escape_string($db, $_POST['name']);

    // SELECT adopter.Email,adopter.PhoneNum, adopter.FirstName, adopter.LastName, adopter.Street, adopter.City, adopter.State, adopter.ZipCode
    // FROM adopter LEFT JOIN application on adopter.Email = application.Email
    // WHERE (LOWER(adopter.LastName) LIKE '%As%' or LOWER(adopter.CoApplicantLastName) LIKE '%As%') and application.ApplicationStatus = 'approved';

    $query = "SELECT adopter.Email,adopter.PhoneNum, adopter.FirstName, adopter.LastName, adopter.Street, adopter.City, adopter.State, adopter.ZipCode, application.ApplicationNum " .
        "FROM adopter LEFT JOIN application on adopter.Email = application.Email " .
        "WHERE (LOWER(adopter.LastName) LIKE '%$name%' or LOWER(adopter.CoApplicantLastName) LIKE '%$name%') and application.ApplicationStatus = 'approved'";

    $result = mysqli_query($db, $query);

    include('lib/show_queries.php');

    if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg,  "SELECT ERROR:Failed to find approved application ... <br>" . __FILE__ . " line:" . __LINE__);
    }
}
?>

<?php include("lib/header.php"); ?>

<title>Add Adoption</title>
</head>

<body>
    <div id="main_container">
        <?php include("lib/menu.php"); ?>

        <div class="center_content">
            <div class="center_left">
                <div class="features">
                    <div class="section">
                        <div class="subtitle">Search for Approved Adoptor</div>
                        <?php
                        $redirecturl = "add_adoption.php?petid=" . $petid;
                        print "<form name='searchform' action='$redirecturl' method='post'>";
                        ?>
                        <table>
                            <tr>
                                <td class="item_label">Last Name</td>
                                <td><input type="text" name="name" /></td>
                            </tr>
                        </table>
                        <a href="javascript:searchform.submit();" class="fancy_button">Search</a>
                        </form>
                    </div>

                    <div class="section">
                        <table>
                            <tr>
                                <th class="heading">Application Num</th>
                                <th class="heading">First Name</th>
                                <th class="heading">Last Name</th>
                                <th class="heading">Email</th>
                                <th class="heading">Phone Number</th>
                                <th class="heading">Street</th>
                                <th class="heading">City</th>
                                <th class="heading">State</th>
                                <th class="heading">ZipCode</th>
                                <th class="heading">Add Adoption</th>
                            </tr>

                            <?php
                            if (isset($result)) {
                                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                                    print "<tr>";
                                    print "<td>{$row['ApplicationNum']}</td>";
                                    print "<td>{$row['FirstName']}</td>";
                                    print "<td>{$row['LastName']}</td>";
                                    print "<td>{$row['Email']}</td>";
                                    print "<td>{$row['PhoneNum']}</td>";
                                    print "<td>{$row['Street']}</td>";
                                    print "<td>{$row['City']}</td>";
                                    print "<td>{$row['State']}</td>";
                                    print "<td>{$row['ZipCode']}</td>";
                                    print '<td><a href="enter_adoption.php?appnum=' . urlencode($row['ApplicationNum']) . '&petid=' . $petid . '">Choose</a></td>';
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