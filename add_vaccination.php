<?php

include('lib/common.php');

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$petid = $_REQUEST['petid'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $vaccinetype = mysqli_real_escape_string($db, $_POST['vaccinetype']);
    $admindate = $_POST['admindate'];
    $expdate = $_POST['expdate'];
    $vaccinationnumber = mysqli_real_escape_string($db, $_POST['vaccinationnumber']);

    if (!empty($vaccinetype)) {
        $due_query = " SELECT PetID FROM Vaccination ".
                     " WHERE PetID = '$petid' and VaccineType = '$vaccinetype' and ExpDate > '$admindate' ";
        $due_result = mysqli_query($db, $due_query);
        if (mysqli_num_rows($due_result)>0) {
            array_push($error_msg, "Vaccine not due yet.");
        } else {
            $query = "INSERT INTO vaccination (VaccineType, AdminDate, Username, VaccinationNumber, ExpDate, PetID) " .
                "VALUES ('$vaccinetype','$admindate','$username','$vaccinationnumber','$expdate','$petid')";

            $result = mysqli_query($db, $query);
            include('lib/show_queries.php');
            if (mysqli_affected_rows($db) == -1) {
                array_push($error_msg,  "INSERT ERROR: vaccination... <br>" .  __FILE__ . " line:" . __LINE__);
            }
        }
    }
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
                <div class="title_name">Add Vaccination</div>
                <div class="features">
                    <div class="section">
                        <div class="subtitle">Enter Vaccination Info</div>
                        <?php
                        $redirecturl = "add_vaccination.php?petid=" . $petid;
                        print "<form name='vaccineform' action='$redirecturl' method='post'>";
                        ?>

                        <table>

                            <tr>
                                <td class="item_label">Vaccine Type</td>
                                <td>
                                    <select name="vaccinetype" required>
                                        <?php
                                        $query = "SELECT VaccineType FROM vaccine LEFT JOIN animal on vaccine.Species = animal.Species " .
                                                "WHERE animal.PetID = $petid and vaccine.VaccineType not in ( " .
                                                "SELECT vaccination.VaccineType FROM vaccination " .
                                                "WHERE vaccination.PetID = $petid and vaccination.ExpDate > now())";

                                        $vaccineresult = mysqli_query($db, $query);
                                        include('lib/show_queries.php');
                                        if (!empty($vaccineresult) && (mysqli_num_rows($vaccineresult) == 0)) {
                                            array_push($error_msg,  "SELECT ERROR: find vaccine list <br>" . __FILE__ . " line:" . __LINE__);
                                        }
                                        while ($row = mysqli_fetch_array($vaccineresult, MYSQLI_ASSOC)) {
                                            $vaccineoption = $row['VaccineType'];
                                            print "<option value = '$vaccineoption'>$vaccineoption</option>";
                                        }
                                        ?>
                                    </select>
                                </td>

                            </tr>
                            <tr>
                                <td class="item_label">Vaccination Date</td>
                                <td>
                                    <input type="date" name="admindate" required>
                                </td>
                            </tr>
                            <tr>
                                <td class="item_label">Next Dose Date</td>
                                <td>
                                    <input type="date" name="expdate" required>
                                </td>
                            </tr>

                            <tr>
                                <td class="item_label">Vaccine/Tag Number (optional)</td>
                                <td>
                                    <input type="text" name="vaccinationnumber">
                                </td>
                            </tr>
                        </table>

                        <a href="javascript:vaccineform.submit();" class="fancy_button">Submit</a>

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