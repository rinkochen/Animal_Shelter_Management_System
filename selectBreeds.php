<?php
include('lib/common.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	if (isset($_POST['selectedSpecies'])) {
	        $selectedSpecies = $_POST['selectedSpecies'];
	        $query = "SELECT Distinct BreedType FROM BREEDTYPE WHERE Species = '{$selectedSpecies}'";
	        $breedresultNew =  mysqli_query($db, $query);
	        // include('lib/show_queries.php');
            $final_str = "";
            while ($row = mysqli_fetch_array($breedresultNew, MYSQLI_ASSOC)) {
                $breedoption = $row['BreedType'];
                $final_str = $final_str . "<option value = '$breedoption'>$breedoption</option>";
            }
            echo $final_str;
	} else {
		echo "hello world";
	}
 }

?>