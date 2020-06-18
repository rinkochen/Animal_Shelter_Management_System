<?php

include('lib/common.php');


if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// identify userrole and save in session variable
$user = $_SESSION['username'];
$_SESSION['role'] = 'Volunteer';

$query = "SELECT * FROM EMPLOYEE WHERE Username = '$user'";
$result = mysqli_query($db, $query);
if (mysqli_num_rows($result) > 0) {
    $_SESSION['role'] = 'Employee';
}
$query = "SELECT * FROM ADMIN WHERE Username = '$user'";
$result = mysqli_query($db, $query);
if (mysqli_num_rows($result) > 0) {
    $_SESSION['role'] = 'Admin';
}

//search for all animals
$query = "SELECT ANIMAL.PetID, ANIMAL.Name, ANIMAL.Species, BREEDS.BreedTypes, ANIMAL.Sex, ANIMAL.AlterationStatus, ANIMAL.Age, " .
    "        CASE WHEN ADOPTION.AdoptionDate IS NULL AND VACCINESTATUS.PetID IS NOT NULL AND ANIMAL.AlterationStatus = 1 THEN 'Adoptable'  " .
    "             ELSE 'Not Adoptable' END as Adoptibility " .
    "        FROM ANIMAL " .
    "        LEFT JOIN (SELECT PetID, GROUP_CONCAT(BreedType Separator '/') as BreedTypes FROM BREED group by PetID) BREEDS " .
    "        ON ANIMAL.PetID = BREEDS.PetID " .
    "        LEFT JOIN ADOPTION " .
    "        ON ANIMAL.PetID = ADOPTION.PetID " .
    "        LEFT JOIN ( " .
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
    "        ON ANIMAL.PetID = VACCINESTATUS.PetID ";


$result = mysqli_query($db, $query);
include('lib/show_queries.php');

if (mysqli_affected_rows($db) == -1) {
    array_push($error_msg,  "SELECT ERROR:Failed to find animals ... <br>" . __FILE__ . " line:" . __LINE__);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $species = mysqli_real_escape_string($db, $_POST['species']);
    $adoptibility = mysqli_real_escape_string($db, $_POST['adoptibility']);

    $query = "SELECT ANIMAL.PetID, ANIMAL.Name, ANIMAL.Species, BREEDS.BreedTypes, ANIMAL.Sex, ANIMAL.AlterationStatus, ANIMAL.Age, " .
        "        CASE WHEN ADOPTION.AdoptionDate IS NULL AND VACCINESTATUS.PetID IS NOT NULL AND ANIMAL.AlterationStatus = 1 THEN 'Adoptable'  " .
        "             ELSE 'Not Adoptable' END as Adoptibility " .
        "        FROM ANIMAL " .
        "        LEFT JOIN (SELECT PetID, GROUP_CONCAT(BreedType Separator '/') as BreedTypes FROM BREED group by PetID) BREEDS " .
        "        ON ANIMAL.PetID = BREEDS.PetID " .
        "        LEFT JOIN ADOPTION " .
        "        ON ANIMAL.PetID = ADOPTION.PetID " .
        "        LEFT JOIN ( " .
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
        "        ON ANIMAL.PetID = VACCINESTATUS.PetID ";

    if (!empty($species)) {
        $query = $query . " HAVING ANIMAL.Species = '$species' ";
    }
    if (!empty($adoptibility)) {
        if (!empty($species)) {
            $query = $query . " and Adoptibility = '$adoptibility' ";
        } else {
            $query = $query . " HAVING Adoptibility = '$adoptibility' ";
        }
    }

    $result = mysqli_query($db, $query);

    include('lib/show_queries.php');

    if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg,  "SELECT ERROR:Failed to filter animals ... <br>" . __FILE__ . " line:" . __LINE__);
    }
}
?>

<?php include("lib/header.php"); ?>
<title>Dashboard</title>
</head>

<body>
    <div id="main_container">
        <?php include("lib/menu.php"); ?>

        <div class="center_content">
            <div class="center_left">
                <div class="title_name">
                    Animal Dashboard
                </div>


                <div class="features">
                    <div class="section">
                        <div class="subtitle">Number of Available Spaces:</div>
                        <table>
                            <?php
                            $query = "SELECT species.Species, species.MaxCapacity-animalcount.count as count " .
                                "FROM (SELECT adoptible.Species, COUNT(*) as count " .
                                "FROM (SELECT ANIMAL.PetID, ANIMAL.Species FROM ANIMAL " .
                                "LEFT JOIN ADOPTION ON ANIMAL.PetID = ADOPTION.PetID " .
                                "WHERE ADOPTION.AdoptionDate IS NULL) adoptible " .
                                "group by adoptible.Species) animalcount " .
                                "LEFT JOIN species " .
                                "ON animalcount.Species = species.Species";

                            // SELECT species.Species, species.MaxCapacity-animalcount.count as count
                            // FROM (SELECT adoptible.Species, COUNT(*) as count
                            //     FROM (SELECT ANIMAL.PetID, ANIMAL.Species FROM ANIMAL
                            //           LEFT JOIN ADOPTION ON ANIMAL.PetID = ADOPTION.PetID
                            //           WHERE ADOPTION.AdoptionDate IS NULL) adoptible
                            //     group by adoptible.Species) animalcount
                            // LEFT JOIN species
                            // ON animalcount.Species = species.Species;
                            $spaceresult = mysqli_query($db, $query);
                            if (!empty($spaceresult) && (mysqli_num_rows($spaceresult) == 0)) {
                                array_push($error_msg,  "SELECT ERROR: find count <br>" . __FILE__ . " line:" . __LINE__);
                            }

                            while ($row = mysqli_fetch_array($spaceresult, MYSQLI_ASSOC)) {
                                print "<tr>";
                                print "<td>{$row['Species']}</td>";
                                print "<td>{$row['count']}</td>";
                                print "</tr>";
                            }
                            ?>
                            <tr>
                                <?php
                                if ($_SESSION['role'] != 'Volunteer') {
                                    print "<td><a href='add_animal.php'>Add Animal</a></td>";
                                }
                                ?>
                            </tr>
                        </table>
                    </div>

                    <div class="section">
                        <form name="filterform" action="dashboard.php" method="post">
                            <table>
                                <tr>
                                    <td class="item_label">Filter by Species</td>
                                    <td>
                                        <select name="species">
                                            <option value=""></option>
                                            <option value="Dog">Dog</option>
                                            <option value="Cat">Cat</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Filter by Adoptibility</td>
                                    <td>
                                        <select name="adoptibility">
                                            <option value=""></option>
                                            <option value="Adoptable">Adoptable</option>
                                            <option value="Not Adoptable">Not Adoptable</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="javascript:filterform.submit();" class="fancy_button">Filter!</a>
                                    </td>
                                </tr>
                            </table>


                        </form>
                    </div>
                    <div class="section">
                        <table>
                            <tr>
                                <th class="heading">Name</th>
                                <th class="heading">Species</th>
                                <th class="heading">Breed</th>
                                <th class="heading">Sex</th>
                                <th class="heading">Alteration Status</th>
                                <th class="heading">Age in Month</th>
                                <th class="heading">Adoptibility</th>
                            </tr>

                            <?php
                            if (isset($result)) {
                                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                                    $petid = $row['PetID'];
                                    print "<tr>";
                                    print "<td><a href='view_animal.php?petid=$petid'>{$row['Name']}</a></td>";
                                    print "<td>{$row['Species']}</td>";
                                    print "<td>{$row['BreedTypes']}</td>";
                                    print "<td>{$row['Sex']}</td>";
                                    print "<td>{$row['AlterationStatus']}</td>";
                                    print "<td>{$row['Age']}</td>";
                                    print "<td>{$row['Adoptibility']}</td>";
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