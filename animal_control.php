<?php

include('lib/common.php');
// written by GTusername4

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
$query = "SELECT extract( YEAR_MONTH FROM SurrenderDate ) as Month, COUNT(*) as CountSurrendered " .
    "FROM animal " .
    "where SurrenderByAnimalControl = 1 and SurrenderDate  > DATE_SUB(now(), INTERVAL 6 MONTH) " .
    "GROUP BY Month " .
    "ORDER BY Month";

// SELECT extract( YEAR_MONTH FROM SurrenderDate ) as Month, COUNT(*) as CountSurrendered
// FROM animal
// where SurrenderByAnimalControl = 1 and SurrenderDate  > DATE_SUB(now(), INTERVAL 6 MONTH)
// GROUP BY Month
// ORDER BY Month;

$surrendercount = mysqli_query($db, $query);
include('lib/show_queries.php');

if (!empty($surrendercount) && (mysqli_num_rows($surrendercount) > 0)) {
    $row = mysqli_fetch_array($surrendercount, MYSQLI_ASSOC);
    $count = mysqli_num_rows($surrendercount);
} else {
    array_push($error_msg,  "SELECT ERROR: surrender profile... <br>" .  __FILE__ . " line:" . __LINE__);
}

$query = "select EXTRACT( YEAR_MONTH FROM ADOPTION.adoptionDate ) as Month, COUNT(*) as CountAdopted " .
    "from ADOPTION " .
    "left join ANIMAL " .
    "on ADOPTION.PetID = ANIMAL.PetID " .
    "where DATEDIFF(ADOPTION.adoptionDate, ANIMAL.SurrenderDate)>=60 and AdoptionDate > DATE_SUB(now(), INTERVAL 6 MONTH)" .
    "group by Month " .
    "order by Month";

// select EXTRACT( YEAR_MONTH FROM ADOPTION.adoptionDate ) as Month, COUNT(*) as CountAdopted
// from ADOPTION 
// left join ANIMAL 
// on ADOPTION.PetID = ANIMAL.PetID
// where DATEDIFF(ADOPTION.adoptionDate, ANIMAL.SurrenderDate)>=60 and AdoptionDate > DATE_SUB(now(), INTERVAL 6 MONTH)
// group by Month
// order by Month;

$adoptioncount = mysqli_query($db, $query);
include('lib/show_queries.php');

if (!empty($adoptioncount) && (mysqli_num_rows($adoptioncount) > 0)) {
    $row = mysqli_fetch_array($adoptioncount, MYSQLI_ASSOC);
    $count = mysqli_num_rows($adoptioncount);
} else {
    array_push($error_msg,  "SELECT ERROR: adoption count ... <br>" .  __FILE__ . " line:" . __LINE__);
}


if (!empty($_GET['view_surrender'])) {

    $month = $_GET['view_surrender'];

    $query = "select ANIMAL.PetID, ANIMAL.Species, BREEDS.BreedTypes, Sex, AlterationStatus, MicrochipID, SurrenderDate ".
            "from ANIMAL " . 
            "left join ( " . 
            "select PetID, GROUP_CONCAT(BreedType Separator '/') as BreedTypes " . 
            "from BREED " . 
            "group by PetID) BREEDS " . 
            "ON ANIMAL.PetID = BREEDS.PetID " . 
            "where ANIMAL.SurrenderByAnimalControl = 1 and EXTRACT( YEAR_MONTH FROM SurrenderDate ) = '$month' ". 
            "order by ANIMAL.PetID";
        // select ANIMAL.PetID, ANIMAL.Species, BREEDS.BreedTypes, Sex, AlterationStatus, MicrochipID, SurrenderDate
        // from ANIMAL
        // left join (
        //     select PetID, GROUP_CONCAT(BreedType Separator '/') as BreedTypes
        //     from BREED
        //     group by PetID) BREEDS
        // ON ANIMAL.PetID = BREEDS.PetID
        // where ANIMAL.SurrenderByAnimalControl = 1 and ANIMAL.SurrenderDate  > DATE_SUB(now(), INTERVAL 6 MONTH)
        // order by ANIMAL.PetID
    $surrender_animal = mysqli_query($db, $query);
    include('lib/show_queries.php');

    if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg,  "DELETE ERROR: surrender animal...<br>" . __FILE__ . " line:" . __LINE__);
    }
}

if (!empty($_GET['view_adopted'])) {

    $month = $_GET['view_adopted'];

    $query = "select ANIMAL.PetID, ANIMAL.Species, BREEDS.BreedTypes, Sex, AlterationStatus, MicrochipID, SurrenderDate, " . 
            "DATEDIFF(ADOPTION.adoptionDate, ANIMAL.SurrenderDate) as ndays " . 
            "from ADOPTION " . 
            "left join ANIMAL " . 
            "on ADOPTION.PetID = ANIMAL.PetID " . 
            "left join ( " . 
            "select PetID, GROUP_CONCAT(BreedType Separator '/') as BreedTypes " . 
            "from BREED " . 
            "group by PetID) BREEDS " . 
            "ON ADOPTION.PetID = BREEDS.PetID " . 
            "where DATEDIFF(ADOPTION.adoptionDate, ANIMAL.SurrenderDate)>=60 and EXTRACT( YEAR_MONTH FROM ADOPTION.adoptionDate ) = '$month' " . 
            "order by ndays DESC";


        //     select ANIMAL.PetID, ANIMAL.Species, BREEDS.BreedTypes, Sex, AlterationStatus, MicrochipID, SurrenderDate,
        //     DATEDIFF(ADOPTION.adoptionDate, ANIMAL.SurrenderDate) as ndays
        // from ADOPTION 
        // left join ANIMAL 
        // on ADOPTION.PetID = ANIMAL.PetID
        // left join (
        //     select PetID, GROUP_CONCAT(BreedType Separator '/') as BreedTypes
        //     from BREED
        //     group by PetID) BREEDS
        // ON ADOPTION.PetID = BREEDS.PetID      
        // where DATEDIFF(ADOPTION.adoptionDate, ANIMAL.SurrenderDate)>=60 and adoption.AdoptionDate  > DATE_SUB(now(), INTERVAL 6 MONTH) 
        // order by ndays DESC

    $adopted_animal = mysqli_query($db, $query);
    include('lib/show_queries.php');

    if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg,  "DELETE ERROR: adopt animal...<br>" . __FILE__ . " line:" . __LINE__);
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
                <div class="title_name">View Animal Control Report</div>
                <div class="features">
                    <div class="section">
                        <div class="subtitle">Number of Animals Surrendered by Animal Control</div>
                        <table>
                            <?php
                            if (isset($surrendercount)) {
                                print "<tr>";
                                print "<td class='heading'>Month</td>";
                                print "<td class='heading'>Count</td>";
                                print "<td class='heading'>View?</td>";
                                print "</tr>";                               
                                while ($row = mysqli_fetch_array($surrendercount, MYSQLI_ASSOC)) {
                                    $month = urlencode($row['Month']);
                                    print "<tr>";
                                    print "<td>{$row['Month']}</td>";
                                    print "<td>{$row['CountSurrendered']}</td>";
                                    print '<td>
                                        <a href="animal_control.php?view_surrender=' . urlencode($row['Month']) . '">View</a>
                                        </td>';
                                    print "</tr>";
                                }
                            }    ?>
                        </table>
                    </div>

                    <div class="section">
                        <div class="subtitle">Details:</div>
                        <table>
                            <?php
                            if (isset($surrender_animal)) {
                                print "<tr>";
                                print "<td class='heading'>Pet ID</td>";
                                print "<td class='heading'>Species</td>";
                                print "<td class='heading'>Breed</td>";
                                print "<td class='heading'>Sex</td>";
                                print "<td class='heading'>Alteration Status</td>";
                                print "<td class='heading'>Microchip ID</td>";
                                print "<td class='heading'>Surrender Date</td>";
                                print "</tr>";                               
                                while ($row = mysqli_fetch_array($surrender_animal, MYSQLI_ASSOC)) {
                                    print "<tr>";
                                        print "<td>{$row['PetID']}</td>";
                                        print "<td>{$row['Species']}</td>";
                                        print "<td>{$row['BreedTypes']}</td>";
                                        print "<td>{$row['Sex']}</td>";
                                        print "<td>{$row['AlterationStatus']}</td>";
                                        print "<td>{$row['MicrochipID']}</td>";
                                        print "<td>{$row['SurrenderDate']}</td>";
                                    print "</tr>";
                                }
                            }    ?>
                        </table>
                    </div>

                    <div class="section">
                        <div class="subtitle">Number of Animals Adopted were in the rescue for 60 or more days</div>
                        <table>
                            <?php
                            if (isset($adoptioncount)) {
                                print "<tr>";
                                print "<td class='heading'>Month</td>";
                                print "<td class='heading'>Count</td>";
                                print "<td class='heading'>View?</td>";
                                print "</tr>";                               
                                while ($row = mysqli_fetch_array($adoptioncount, MYSQLI_ASSOC)) {
                                    $month = urlencode($row['Month']);
                                    print "<tr>";
                                    print "<td>{$row['Month']}</td>";
                                    print "<td>{$row['CountAdopted']}</td>";
                                    print '<td>
                                        <a href="animal_control.php?view_adopted=' . urlencode($row['Month']) . '">View</a>
                                        </td>';
                                    print "</tr>";
                                }
                            }    ?>
                        </table>
                    </div>

                    <div class="section">
                        <div class="subtitle">Details:</div>
                        <table>
                            <?php
                            if (isset($adopted_animal)) {
                                print "<tr>";
                                print "<td class='heading'>Pet ID</td>";
                                print "<td class='heading'>Species</td>";
                                print "<td class='heading'>Breed</td>";
                                print "<td class='heading'>Sex</td>";
                                print "<td class='heading'>Alteration Status</td>";
                                print "<td class='heading'>Microchip ID</td>";
                                print "<td class='heading'>Surrender Date</td>";
                                print "<td class='heading'>Days in Shelter</td>";
                                print "</tr>";                               
                                while ($row = mysqli_fetch_array($adopted_animal, MYSQLI_ASSOC)) {
                                    print "<tr>";
                                        print "<td>{$row['PetID']}</td>";
                                        print "<td>{$row['Species']}</td>";
                                        print "<td>{$row['BreedTypes']}</td>";
                                        print "<td>{$row['Sex']}</td>";
                                        print "<td>{$row['AlterationStatus']}</td>";
                                        print "<td>{$row['MicrochipID']}</td>";
                                        print "<td>{$row['SurrenderDate']}</td>";
                                        print "<td>{$row['ndays']}</td>";
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