<?php

include('lib/common.php');

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$petid = $_REQUEST['petid'];
$appnum = $_REQUEST['appnum'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $adoptionfee = $_POST['fee'];
    $adoptiondate = $_POST['date'];

    // SELECT adopter.Email,adopter.PhoneNum, adopter.FirstName, adopter.LastName, adopter.Street, adopter.City, adopter.State, adopter.ZipCode
    // FROM adopter LEFT JOIN application on adopter.Email = application.Email
    // WHERE (LOWER(adopter.LastName) LIKE '%As%' or LOWER(adopter.CoApplicantLastName) LIKE '%As%') and application.ApplicationStatus = 'approved';

    $query = "INSERT INTO adoption (ApplicationNum, PetID, AdoptionDate, AdoptionFee) " .
        "VALUES ('$appnum','$petid','$adoptiondate','$adoptionfee')";

    $result = mysqli_query($db, $query);
    $id = mysqli_insert_id($db);
    include('lib/show_queries.php');

    if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg,  "INSERT ERROR: adoption... <br>" .  __FILE__ . " line:" . __LINE__);
    }

    array_push($query_msg, "inserting ... ");
    header(REFRESH_TIME . 'url=dashboard.php');	
}
?>

<?php include("lib/header.php"); ?>

<title>Enter Adoption</title>
</head>

<body>
    <div id="main_container">
        <?php include("lib/menu.php"); ?>

        <div class="center_content">
            <div class="center_left">
                <div class="features">
                    <div class="section">
                        <div class="subtitle">Enter Adoption Info</div>
                        <?php
                        $redirecturl = "enter_adoption.php?appnum=" . $appnum . "&petid=" . $petid;
                        print "<form name='adoptionform' action='$redirecturl' method='post'>";
                        ?>
                        <table>
                            <tr>
                                <td class="item_label">Adoption Date</td>
                                <td><input type="date" name="date"></td>
                            </tr>
                            <tr>
                                <td class="item_label">Adoption Fee</td>
                                <td><input type="number" step="0.01" name="fee" /></td>
                            </tr>
                        </table>
                        <a href="javascript:adoptionform.submit();" class="fancy_button">Submit</a>
                        </form>
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