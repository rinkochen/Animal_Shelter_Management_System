<?php

include('lib/common.php');

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$petid = $_REQUEST['petid'];
$query = "SELECT Species FROM ANIMAL WHERE PetID = '$petid' ";
$result = mysqli_query($db, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$species = $row['Species'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $breedlist = $_POST['breedlist'];
    $sex = $_POST['sex'];
    $alterationstatus = $_POST['alterationstatus'];
    $microchipid = mysqli_real_escape_string($db, $_POST['microchipid']);

    if (!empty($sex)) {
        $query = "UPDATE ANIMAL SET Sex = '$sex' WHERE PetID = '$petid' ";
    }
    $result = mysqli_query($db, $query);
    include('lib/show_queries.php');
    if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg,  "UPDATE ERROR: update sex ... <br>" .  __FILE__ . " line:" . __LINE__);
    }

    if (!empty($alterationstatus)) {
        $query = "UPDATE ANIMAL SET alterationstatus = '$alterationstatus' WHERE PetID = '$petid' ";
    }
    $result = mysqli_query($db, $query);
    include('lib/show_queries.php');
    if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg,  "UPDATE ERROR: update alteration ... <br>" .  __FILE__ . " line:" . __LINE__);
    }

    if (!empty($microchipid)) {
        $query = "UPDATE ANIMAL SET MicrochipID = '$microchipid' WHERE PetID = '$petid' ";
    }
    $result = mysqli_query($db, $query);
    include('lib/show_queries.php');
    if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg,  "UPDATE ERROR: update micropchipid ... <br>" .  __FILE__ . " line:" . __LINE__);
    }

    if (!empty($breedlist)) {
        if ((in_array("Unknown", $breedlist) or in_array("Mixed", $breedlist)) && count($breedlist) > 1) {
            array_push($error_msg, "Unknown or Mixed should be selected as the only breed");
        } else {
            $query = "DELETE FROM BREED " .
                "WHERE PetID = '$petid' ";
            $result = mysqli_query($db, $query);

            foreach ($breedlist as $breed) {
                $query = "INSERT INTO BREED (BreedType, PetID, Species) " .
                    "VALUES ('$breed','$petid','$species')";
                $result = mysqli_query($db, $query);

                include('lib/show_queries.php');
                if ($result  == False) {
                    array_push($error_msg, "INSERT error: breed <br>" .  __FILE__ . " line:" . __LINE__);
                }
            }
        }
    }
}

//query for animal detaills
$query = "SELECT animal.PetID, animal.Name, animal.Species, BREEDS.BreedTypes, animal.Sex, animal.AlterationStatus, animal.Age, animal.Description,animal.MicrochipID, animal.SurrenderDate, animal.SurrenderReason, animal.SurrenderByAnimalControl, " .
    "        CASE WHEN adoption.PetID IS NULL THEN 1 ELSE 0 END as Notadopted, CASE WHEN adoption.PetID IS NULL and VACCINESTATUS.PetID IS NOT NULL AND ANIMAL.AlterationStatus = 1 THEN 1 ELSE 0 END as adoptibility " .
    "FROM animal LEFT JOIN (SELECT PetID, GROUP_CONCAT(BreedType Separator '/') as BreedTypes FROM BREED group by PetID) BREEDS " .
    "ON animal.PetID = BREEDS.PetID LEFT JOIN adoption ON animal.PetID = adoption.PetID " .
    "LEFT JOIN ( " .
    "           select a.petid, a.species, a.cnt, b.total_cnt from ( " .
    "                select petid, species, count(*) as cnt from ( " .
    "                    select a.petid, a.vaccinetype, a.expdate, b.species, c.required " .
    "                    from ( " .
    "                        select petid, vaccinetype, max(expdate) as expdate " .
    "                        from vaccination " .
    "                        group by 1,2) a " .
    "                    left join animal b  " .
    "                    on a.petid = b.PetID " .
    "                    left join vaccine c " .
    "                    on a.vaccinetype = c.vaccinetype and b.species = c.species " .
    "                    where c.required = 1) a " .
    "                   group by 1,2) a " .
    "                left join (select species, count(*) as total_cnt from vaccine where required = 1 group by 1) b " .
    "                on a.species = b.species " .
    "                where cnt = total_cnt " .
    "        ) VACCINESTATUS " .
    "        ON ANIMAL.PetID = VACCINESTATUS.PetID WHERE animal.PetID = $petid";


$result = mysqli_query($db, $query);
include('lib/show_queries.php');

if (!is_bool($result) && (mysqli_num_rows($result) > 0)) {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
} else {
    array_push($error_msg,  "Query ERROR: Failed to get animal detail...<br>" . __FILE__ . " line:" . __LINE__);
}



//query for vaccination history
$query = "SELECT * FROM vaccination WHERE PetID = $petid ";
$vaccineresult = mysqli_query($db, $query);

?>

<?php include("lib/header.php"); ?>

<title>View Animal</title>
</head>

<body>
    <div id="main_container">
        <?php include("lib/menu.php"); ?>

        <div class="center_content">
            <div class="center_left">
                <div class="title_name">
                    <?php print $row['Name']; ?>
                </div>
                <div class="features">
                    <div class="section">
                        <?php
                        $redirecturl = "view_animal.php?petid=" . $petid;
                        print "<form name='updateform' action='$redirecturl' method='post'>";
                        ?>
                        <table>
                            <tr>
                                <td class="item_label">Sex</td>
                                <td>
                                    <?php if ($row['Sex'] == 'male') {
                                        print 'Male';
                                    } elseif ($row['Sex'] == 'female') {
                                        print 'Female';
                                    } else {
                                        print '<select name="sex">';
                                        print "<option value='male'>Male</option>";
                                        print "<option value='female'>Female</option>";
                                        print "<option value='unknown' selected = 'true'>Unknown</option>";
                                        print '</select>';
                                    } ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="item_label">Species</td>
                                <td>
                                    <?php print $row['Species']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="item_label">Breed</td>
                                <td>
                                    <?php
                                    if ($row['BreedTypes'] != 'Unknown' && $row['BreedTypes'] != 'Mixed') {
                                        print $row['BreedTypes'];
                                    } else {
                                        print "<select name='breedlist[]' multiple>";
                                        $query = "SELECT Distinct BreedType FROM BREEDTYPE";
                                        $breedresult = mysqli_query($db, $query);
                                        include('lib/show_queries.php');
                                        if (!empty($breedresult) && (mysqli_num_rows($breedresult) == 0)) {
                                            array_push($error_msg,  "SELECT ERROR: find breed <br>" . __FILE__ . " line:" . __LINE__);
                                        }
                                        while ($breedrow = mysqli_fetch_array($breedresult, MYSQLI_ASSOC)) {
                                            $breedoption = $breedrow['BreedType'];
                                            print "<option value = '$breedoption'";
                                            if ($breedoption == $row['BreedTypes']) {
                                                print 'selected="true"';
                                            }
                                            print ">$breedoption</option>";
                                        }
                                        print "</select>";
                                    }
                                    ?>
                                </td>

                            </tr>

                            <tr>
                                <td class="item_label">Alteration Status</td>
                                <td>
                                    <?php
                                    if ($row['AlterationStatus'] == 1) {
                                        print 'Altered';
                                    } else {
                                        print '<select name="alterationstatus" required>';
                                        print "<option value=1>Altered</option>";
                                        print "<option value=0 selected = 'true'>Not yet altered</option>";
                                        print "</select>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="item_label">Age</td>
                                <td>
                                    <?php print $row['Age']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="item_label">Description</td>
                                <td>
                                    <?php print $row['Description']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="item_label">Microchip ID</td>
                                <td>
                                    <?php
                                    if (!empty($row['MicrochipID'])) {
                                        print $row['MicrochipID'];
                                    } else {
                                        print "<input type='text' name='microchipid'>";
                                    }

                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="item_label">Surrender Date</td>
                                <td>
                                    <?php print $row['SurrenderDate']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="item_label">Surrender Reason</td>
                                <td>
                                    <?php print $row['SurrenderReason']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="item_label">Surrendered By Animal Control?</td>
                                <td>
                                    <?php if ($row['SurrenderByAnimalControl'] == 1) {
                                        print 'Yes';
                                    } else {
                                        print 'No';
                                    } ?>
                                </td>
                            </tr>
                            <?php
                            if ($row['adoptibility'] == 1 && $_SESSION['role'] != 'Volunteer') {
                                print "<tr>";
                                print "<td>";
                                print "<a href='add_adoption.php?petid=$petid'>Add Adoption</a>";
                                print "</td>";
                                print "</tr>";
                            }
                            ?>
                            <?php
                            if ($row['Notadopted'] == 1) {
                                print "<tr>";
                                print "<td>";
                                print "<a href='add_vaccination.php?petid=$petid'>Add Vaccination</a>";
                                print "</td>";
                                print "</tr>";
                            }
                            ?>
                        </table>
                        <a href="javascript:updateform.submit();" class="fancy_button">Update</a>

                        </form>
                    </div>


                    <div class="section">
                        <div class="subtitle">Vaccination History</div>
                        <table>
                            <tr>
                                <th class="heading">Vaccine Type</th>
                                <th class="heading">Vaccine Number</th>
                                <th class="heading">Administer Date</th>
                                <th class="heading">Due Date</th>
                            </tr>

                            <?php
                            if (isset($vaccineresult)) {
                                while ($row = mysqli_fetch_array($vaccineresult, MYSQLI_ASSOC)) {
                                    print "<tr>";
                                    print "<td>{$row['VaccineType']}</td>";
                                    print "<td>{$row['VaccinationNumber']}</td>";
                                    print "<td>{$row['AdminDate']}</td>";
                                    print "<td>{$row['ExpDate']}</td>";
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