<?php

include('lib/common.php');
// written by GTusername4

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = mysqli_real_escape_string($db, $_POST['name']);
    $species = $_POST['species'];
    $breedlist = $_POST['breedlist'];
    $sex = $_POST['sex'];
    $alterationstatus = $_POST['alterationstatus'];
    $age = $_POST['age'];
    $description = mysqli_real_escape_string($db, $_POST['description']);
    $microchipid = mysqli_real_escape_string($db, $_POST['microchipid']);
    $surrenderdate = $_POST['surrenderdate'];
    $surrenderreason = mysqli_real_escape_string($db, $_POST['surrenderreason']);
    $animalcontrol = $_POST['animalcontrol'];
    $username = $_SESSION['username'];

    $query = "SELECT species.MaxCapacity-animalcount.count as count " .
         "FROM (SELECT adoptible.Species, COUNT(*) as count " .
         "      FROM (SELECT ANIMAL.PetID, ANIMAL.Species FROM ANIMAL " .
         "      LEFT JOIN ADOPTION ON ANIMAL.PetID = ADOPTION.PetID " .
         "       WHERE ADOPTION.AdoptionDate IS NULL) adoptible " .
         "       group by adoptible.Species) animalcount " .
         "LEFT JOIN species " .
         "ON animalcount.Species = species.Species " .
         "WHERE animalcount.Species = '{$species}' ";

    $spaceresult = mysqli_fetch_array(mysqli_query($db, $query), MYSQLI_ASSOC);

    if (in_array("Unknown", $breedlist) && count($breedlist) > 1) {
        array_push($error_msg, "Unknown should be selected as the only breed");
    } elseif ($spaceresult['count'] <= 0) {
        array_push($error_msg, "No available space");
    } else {
        if (!empty($name)) {
            $query = "INSERT INTO animal (AlterationStatus, Name, Sex, Age, Description, Species, SurrenderReason, SurrenderByAnimalControl, SurrenderDate, MicrochipID, Username) " .
                "VALUES ('$alterationstatus','$name','$sex','$age','$description','$species','$surrenderreason','$animalcontrol','$surrenderdate','$microchipid','$username')";

            $result = mysqli_query($db, $query);
            $id = mysqli_insert_id($db);
            include('lib/show_queries.php');

            if (mysqli_affected_rows($db) == -1) {
                array_push($error_msg,  "INSERT ERROR: animal... <br>" .  __FILE__ . " line:" . __LINE__);
            }

            foreach ($breedlist as $breed) {
                $query = "INSERT INTO BREED (BreedType, PetID, Species) " .
                    "VALUES ('$breed','$id','$species')";
                $result = mysqli_query($db, $query);

                include('lib/show_queries.php');
                if ($result  == False) {
                    array_push($error_msg, "breed insert '" . $interest .  "'  already an interest... <br>" .  __FILE__ . " line:" . __LINE__);
                }
                
            }
            $refreshurl = 'url=view_animal.php?petid=' . $id;
            header(REFRESH_TIME . $refreshurl); 
        }
    }
}

?>

<?php include("lib/header.php"); ?>

<title>Add Animal</title>
</head>

<body>
    <div id="main_container">
        <?php include("lib/menu.php"); ?>

        <div class="center_content">
            <div class="center_left">
                <div class="title_name">Add Animal</div>
                <div class="features">
                    <div class="section">
                        <div class="subtitle">Enter Animal Info</div>
                        <form name="animalform" action="add_animal.php" method="post">
                            <table>
                                <tr>
                                    <td class="item_label">Animal Name</td>
                                    <td>
                                        <input type="text" name="name" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Species</td>
                                    <td>
                                        <select name="species" onchange="OnSelectionChange()" id="selectedSpecies" required>
                                            <?php
                                            $query = "SELECT distinct Species FROM Species";

                                            $speciesresult = mysqli_query($db, $query);
                                            include('lib/show_queries.php');
                                            if (!empty($speciesresult) && (mysqli_num_rows($speciesresult) == 0)) {
                                                array_push($error_msg,  "SELECT ERROR: find species <br>" . __FILE__ . " line:" . __LINE__);
                                            }
                                            while ($row = mysqli_fetch_array($speciesresult, MYSQLI_ASSOC)) {
                                                $speciesoption = $row['Species'];
                                                print "<option value = '$speciesoption'>$speciesoption</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>

                                </tr>
                                <tr>
                                    <td class="item_label">Breed (hold ctrl to select multiple)</td>
                                    <td>
                                        <select name="breedlist[]" id="multipleSelection" multiple required>
                                            <?php
                                            $query = "SELECT Distinct BreedType FROM BREEDTYPE";
                                            $breedresult = mysqli_query($db, $query);
                                            include('lib/show_queries.php');
                                            if (!empty($breedresult) && (mysqli_num_rows($breedresult) == 0)) {
                                                array_push($error_msg,  "SELECT ERROR: find breed <br>" . __FILE__ . " line:" . __LINE__);
                                            }
                                            while ($row = mysqli_fetch_array($breedresult, MYSQLI_ASSOC)) {
                                                $breedoption = $row['BreedType'];
                                                print "<option value = '$breedoption'>$breedoption</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Sex</td>
                                    <td>
                                        <select name="sex" required>
                                            <option value='male'>Male</option>
                                            <option value='female'>Female</option>
                                            <option value='unknown'>Unknown</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Alteration Status</td>
                                    <td>
                                        <select name="alterationstatus" required>
                                            <option value=1>Altered</option>
                                            <option value=0>Not yet altered</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Age in Month</td>
                                    <td>
                                        <input type="number" name="age" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Description</td>
                                    <td>
                                        <input type="text" name="description" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Microchip ID</td>
                                    <td>
                                        <input type="text" name="microchipid">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Surrender Date</td>
                                    <td>
                                        <input type="date" name="surrenderdate">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Surrender Reason</td>
                                    <td>
                                        <input type="text" name="surrenderreason" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Surrendered by Animal Control?</td>
                                    <td>
                                        <select name="animalcontrol" required>
                                            <option value=1>Yes</option>
                                            <option value=0>No</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>

                            <a href="javascript:animalform.submit();" class="fancy_button">Submit</a>

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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
    function OnSelectionChange() {
        var selectedSpecies = document.getElementById("selectedSpecies").value;
        console.log(selectedSpecies);
        $.post('selectBreeds.php', { selectedSpecies: selectedSpecies }, function(data){
            console.log(data);
            document.getElementById("multipleSelection").innerHTML = data;
        });
        console.log(selectedSpecies);
    }
</script>

</html>