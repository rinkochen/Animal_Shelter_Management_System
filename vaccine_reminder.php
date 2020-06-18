<?php

include('lib/common.php');
// written by GTusername4

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$query = "SELECT VaccineType, ExpDate, animal.PetID, animal.Species, BREEDS.BreedTypes, animal.Sex, animal.AlterationStatus, animal.MicrochipID, animal.SurrenderDate, CONCAT(user.FirstName,user.LastName)  as Name " .
    "FROM( SELECT PetID, VaccineType, ExpDate, Username FROM vaccination where ExpDate <= DATE_ADD(now(), INTERVAL 3 MONTH) and ExpDate >= now()) expiring " .
    "LEFT JOIN (SELECT PetID, GROUP_CONCAT(BreedType Separator '/') as BreedTypes FROM BREED group by PetID) BREEDS ON expiring.PetID = BREEDS.PetID LEFT JOIN animal ON expiring.PetID = animal.PetID " .
    "LEFT JOIN USER on expiring.Username = user.Username ORDER BY ExpDate";

// SELECT VaccineType, ExpDate, animal.PetID, animal.Species, BREEDS.BreedTypes, animal.Sex, animal.AlterationStatus, animal.MicrochipID, animal.SurrenderDate, CONCAT(user.FirstName,user.LastName)  as Name
// FROM( SELECT PetID, VaccineType, ExpDate, Username FROM vaccination where ExpDate <= DATE_ADD(now(), INTERVAL 3 MONTH) and ExpDate >= now()) expiring
// LEFT JOIN (SELECT PetID, GROUP_CONCAT(BreedType Separator '/') as BreedTypes FROM BREED group by PetID) BREEDS ON expiring.PetID = BREEDS.PetID LEFT JOIN animal ON expiring.PetID = animal.PetID
// LEFT JOIN USER on expiring.Username = user.Username;


$result = mysqli_query($db, $query);
include('lib/show_queries.php');

if (mysqli_affected_rows($db) == -1) {
    array_push($error_msg,  "DELETE ERROR: expiring vaccination...<br>" . __FILE__ . " line:" . __LINE__);
}


?>

<?php include("lib/header.php"); ?>

<title>Vaccine Reminder</title>
</head>

<body>
    <div id="main_container">
        <?php include("lib/menu.php"); ?>

        <div class="center_content">
            <div class="center_left">
                <div class="title_name">Vaccine Reminder Report</div>
                <div class="features">
                    <div class="section">
                        <table>
                            <tr>
                                <th class="heading">Vaccine Type</th>
                                <th class="heading">Due Date</th>
                                <th class="heading">Pet ID</th>
                                <th class="heading">Species</th>
                                <th class="heading">Breed</th>
                                <th class="heading">Sex</th>
                                <th class="heading">Alteration Status</th>
                                <th class="heading">Microchip ID</th>
                                <th class="heading">Surrender Date</th>
                                <th class="heading">Last Administered By</th>
                            </tr>

                            <?php
                            if (isset($result)) {
                                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                                    print "<tr>";
                                    print "<td>{$row['VaccineType']}</td>";
                                    print "<td>{$row['ExpDate']}</td>";
                                    print "<td>{$row['PetID']}</td>";
                                    print "<td>{$row['Species']}</td>";
                                    print "<td>{$row['BreedTypes']}</td>";
                                    print "<td>{$row['Sex']}</td>";
                                    print "<td>{$row['AlterationStatus']}</td>";
                                    print "<td>{$row['MicrochipID']}</td>";
                                    print "<td>{$row['SurrenderDate']}</td>";
                                    print "<td>{$row['Name']}</td>";

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