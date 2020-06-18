<?php

include('lib/common.php');
// written by GTusername4

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}



$query = "  SELECT a.Month, a.Species, a.BreedTypes, a.nsurrender, b.nadopted " .
         "FROM( SELECT EXTRACT( YEAR_MONTH FROM surrenderanimal.SurrenderDate ) as Month, Species, BreedTypes, COUNT(*) as nsurrender " .
         "FROM( SELECT animal.PetID, animal.SurrenderDate, animal.Species, GROUP_CONCAT(BreedType Separator '/') as BreedTypes " .
         " FROM animal left join breed on animal.PetID = breed.PetID  WHERE SurrenderDate > DATE_SUB(now(), INTERVAL 12 MONTH) " .
         " group by animal.PetID, SurrenderDate, Species ) surrenderanimal GROUP BY Month, Species, BreedTypes   " .
         " UNION SELECT EXTRACT( YEAR_MONTH FROM surrenderanimal.SurrenderDate ) as Month, Species, 'Total' as BreedTypes, COUNT(*) as nsurrender " .
         " FROM( SELECT animal.PetID, animal.SurrenderDate, animal.Species, GROUP_CONCAT(BreedType Separator '/') as BreedTypes " .
         " FROM animal left join breed on animal.PetID = breed.PetID WHERE SurrenderDate > DATE_SUB(now(), INTERVAL 12 MONTH) " .
          "group by animal.PetID, SurrenderDate, Species ) surrenderanimal GROUP BY Month, Species) a " .
         "LEFT OUTER JOIN ( SELECT EXTRACT( YEAR_MONTH FROM adoptedanimal.adoptionDate ) as Month, Species, BreedTypes, COUNT(*) as nadopted " .
          "FROM( SELECT adoption.PetID, AdoptionDate, Species, GROUP_CONCAT(BreedType Separator '/') as BreedTypes " .
         " FROM adoption left join breed on adoption.PetID = breed.PetID WHERE AdoptionDate > DATE_SUB(now(), INTERVAL 12 MONTH) " .
          "group by adoption.PetID, AdoptionDate, Species ) adoptedanimal GROUP BY Month, Species, BreedTypes " .
          "UNION SELECT EXTRACT( YEAR_MONTH FROM adoptedanimal.adoptionDate ) as Month, Species, 'Total' as BreedTypes, COUNT(*) as nadopted " .
         " FROM( SELECT adoption.PetID, AdoptionDate, Species, GROUP_CONCAT(BreedType Separator '/') as BreedTypes FROM adoption " .
         " left join breed on adoption.PetID = breed.PetID WHERE AdoptionDate > DATE_SUB(now(), INTERVAL 12 MONTH) " .
          "group by adoption.PetID, AdoptionDate, Species ) adoptedanimal GROUP BY Month, Species) b      " .
         "ON a.Month = b.Month AND a.Species = b.Species AND a.BreedTypes = b.BreedTypes " .
         "UNION SELECT b.Month, b.Species, b.BreedTypes, a.nsurrender, b.nadopted FROM( SELECT EXTRACT( YEAR_MONTH FROM surrenderanimal.SurrenderDate ) as Month, Species, BreedTypes, COUNT(*) as nsurrender " .
         " FROM( SELECT animal.PetID, animal.SurrenderDate, animal.Species, GROUP_CONCAT(BreedType Separator '/') as BreedTypes FROM animal left join breed on animal.PetID = breed.PetID " .
          "WHERE SurrenderDate > DATE_SUB(now(), INTERVAL 12 MONTH) group by animal.PetID, SurrenderDate, Species ) surrenderanimal GROUP BY Month, Species, BreedTypes UNION         ".
        "SELECT EXTRACT( YEAR_MONTH FROM surrenderanimal.SurrenderDate ) as Month, Species, 'Total' as BreedTypes, COUNT(*) as nsurrender FROM( SELECT animal.PetID, animal.SurrenderDate, animal.Species, GROUP_CONCAT(BreedType Separator '/') as BreedTypes " .
         " FROM animal left join breed on animal.PetID = breed.PetID WHERE SurrenderDate > DATE_SUB(now(), INTERVAL 12 MONTH) group by animal.PetID, SurrenderDate, Species ) surrenderanimal " .
         " GROUP BY Month, Species) a RIGHT OUTER JOIN ( SELECT EXTRACT( YEAR_MONTH FROM adoptedanimal.adoptionDate ) as Month, Species, BreedTypes, COUNT(*) as nadopted " .
         " FROM( SELECT adoption.PetID, AdoptionDate, Species, GROUP_CONCAT(BreedType Separator '/') as BreedTypes FROM adoption " .
        "  left join breed on adoption.PetID = breed.PetID WHERE AdoptionDate > DATE_SUB(now(), INTERVAL 12 MONTH) group by adoption.PetID, AdoptionDate, Species ) adoptedanimal " .
          "GROUP BY Month, Species, BreedTypes UNION SELECT EXTRACT( YEAR_MONTH FROM adoptedanimal.adoptionDate ) as Month, Species, 'Total' as BreedTypes, COUNT(*) as nadopted FROM( " .
          "SELECT adoption.PetID, AdoptionDate, Species, GROUP_CONCAT(BreedType Separator '/') as BreedTypes FROM adoption left join breed on adoption.PetID = breed.PetID " .
         " WHERE AdoptionDate > DATE_SUB(now(), INTERVAL 12 MONTH) group by adoption.PetID, AdoptionDate, Species ) adoptedanimal GROUP BY Month, Species) b      " .
        "ON a.Month = b.Month AND a.Species = b.Species AND a.BreedTypes = b.BreedTypes ORDER BY Month, Species, BreedTypes ";

    // SELECT a.Month, a.Species, a.BreedTypes, a.nsurrender, b.nadopted
    // FROM(  
    //             SELECT EXTRACT( YEAR_MONTH FROM surrenderanimal.SurrenderDate ) as Month, Species, BreedTypes, COUNT(*) as nsurrender
    //           FROM(
    //           SELECT animal.PetID, animal.SurrenderDate, animal.Species, GROUP_CONCAT(BreedType Separator '/') as BreedTypes
    //           FROM animal
    //           left join breed
    //           on animal.PetID = breed.PetID
    //           WHERE SurrenderDate > DATE_SUB(now(), INTERVAL 12 MONTH)
    //           group by animal.PetID, SurrenderDate, Species ) surrenderanimal
    //           GROUP BY Month, Species, BreedTypes            
    //           UNION            
    //           SELECT EXTRACT( YEAR_MONTH FROM surrenderanimal.SurrenderDate ) as Month, Species, 'Total' as BreedTypes, COUNT(*) as nsurrender
    //           FROM(
    //           SELECT animal.PetID, animal.SurrenderDate, animal.Species, GROUP_CONCAT(BreedType Separator '/') as BreedTypes
    //           FROM animal
    //           left join breed
    //           on animal.PetID = breed.PetID
    //           WHERE SurrenderDate > DATE_SUB(now(), INTERVAL 12 MONTH)
    //           group by animal.PetID, SurrenderDate, Species ) surrenderanimal
    //           GROUP BY Month, Species) a
    // LEFT OUTER JOIN (
    //           SELECT EXTRACT( YEAR_MONTH FROM adoptedanimal.adoptionDate ) as Month, Species, BreedTypes, COUNT(*) as nadopted
    //           FROM(
    //           SELECT adoption.PetID, AdoptionDate, Species, GROUP_CONCAT(BreedType Separator '/') as BreedTypes
    //           FROM adoption
    //           left join breed
    //           on adoption.PetID = breed.PetID
    //           WHERE AdoptionDate > DATE_SUB(now(), INTERVAL 12 MONTH)
    //           group by adoption.PetID, AdoptionDate, Species ) adoptedanimal
    //           GROUP BY Month, Species, BreedTypes
    //           UNION
    //           SELECT EXTRACT( YEAR_MONTH FROM adoptedanimal.adoptionDate ) as Month, Species, 'Total' as BreedTypes, COUNT(*) as nadopted
    //           FROM(
    //           SELECT adoption.PetID, AdoptionDate, Species, GROUP_CONCAT(BreedType Separator '/') as BreedTypes
    //           FROM adoption
    //           left join breed
    //           on adoption.PetID = breed.PetID
    //           WHERE AdoptionDate > DATE_SUB(now(), INTERVAL 12 MONTH)
    //           group by adoption.PetID, AdoptionDate, Species ) adoptedanimal
    //           GROUP BY Month, Species) b      
    // ON a.Month = b.Month AND a.Species = b.Species AND a.BreedTypes = b.BreedTypes
    
    // UNION
    
    // SELECT a.Month, a.Species, a.BreedTypes, a.nsurrender, b.nadopted
    // FROM(  
    //             SELECT EXTRACT( YEAR_MONTH FROM surrenderanimal.SurrenderDate ) as Month, Species, BreedTypes, COUNT(*) as nsurrender
    //           FROM(
    //           SELECT animal.PetID, animal.SurrenderDate, animal.Species, GROUP_CONCAT(BreedType Separator '/') as BreedTypes
    //           FROM animal
    //           left join breed
    //           on animal.PetID = breed.PetID
    //           WHERE SurrenderDate > DATE_SUB(now(), INTERVAL 12 MONTH)
    //           group by animal.PetID, SurrenderDate, Species ) surrenderanimal
    //           GROUP BY Month, Species, BreedTypes            
    //           UNION            
    //           SELECT EXTRACT( YEAR_MONTH FROM surrenderanimal.SurrenderDate ) as Month, Species, 'Total' as BreedTypes, COUNT(*) as nsurrender
    //           FROM(
    //           SELECT animal.PetID, animal.SurrenderDate, animal.Species, GROUP_CONCAT(BreedType Separator '/') as BreedTypes
    //           FROM animal
    //           left join breed
    //           on animal.PetID = breed.PetID
    //           WHERE SurrenderDate > DATE_SUB(now(), INTERVAL 12 MONTH)
    //           group by animal.PetID, SurrenderDate, Species ) surrenderanimal
    //           GROUP BY Month, Species) a
    // RIGHT OUTER JOIN (
    //           SELECT EXTRACT( YEAR_MONTH FROM adoptedanimal.adoptionDate ) as Month, Species, BreedTypes, COUNT(*) as nadopted
    //           FROM(
    //           SELECT adoption.PetID, AdoptionDate, Species, GROUP_CONCAT(BreedType Separator '/') as BreedTypes
    //           FROM adoption
    //           left join breed
    //           on adoption.PetID = breed.PetID
    //           WHERE AdoptionDate > DATE_SUB(now(), INTERVAL 12 MONTH)
    //           group by adoption.PetID, AdoptionDate, Species ) adoptedanimal
    //           GROUP BY Month, Species, BreedTypes
    //           UNION
    //           SELECT EXTRACT( YEAR_MONTH FROM adoptedanimal.adoptionDate ) as Month, Species, 'Total' as BreedTypes, COUNT(*) as nadopted
    //           FROM(
    //           SELECT adoption.PetID, AdoptionDate, Species, GROUP_CONCAT(BreedType Separator '/') as BreedTypes
    //           FROM adoption
    //           left join breed
    //           on adoption.PetID = breed.PetID
    //           WHERE AdoptionDate > DATE_SUB(now(), INTERVAL 12 MONTH)
    //           group by adoption.PetID, AdoptionDate, Species ) adoptedanimal
    //           GROUP BY Month, Species) b      
    // ON a.Month = b.Month AND a.Species = b.Species AND a.BreedTypes = b.BreedTypes
    // ORDER BY Month, Species, BreedTypes; 


$result = mysqli_query($db, $query);
include('lib/show_queries.php');

if (mysqli_affected_rows($db) == -1) {
    array_push($error_msg,  "DELETE ERROR: monthly adoption...<br>" . __FILE__ . " line:" . __LINE__);
}


?>

<?php include("lib/header.php"); ?>

<title>Monthly Adoption Report</title>
</head>

<body>
    <div id="main_container">
        <?php include("lib/menu.php"); ?>

        <div class="center_content">
            <div class="center_left">
                <div class="title_name">Monthly Adoption Report</div>
                <div class="features">
                    <div class="section">
                    <table>
                            <tr>
                                <th class="heading">Month</th>
                                <th class="heading">Species</th>
                                <th class="heading">Breed</th>
                                <th class="heading">Number of Surrenders</th>
                                <th class="heading">Number of Adoptions</th>
                            </tr>

                            <?php
                            if (isset($result)) {
                                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                                    $petid = urlencode($row['PetID']);
                                    print "<tr>";
                                    print "<td>{$row['Month']}</td>";
                                    print "<td>{$row['Species']}</td>";
                                    print "<td>{$row['BreedTypes']}</td>";
                                    print "<td>{$row['nsurrender']}</td>";
                                    print "<td>{$row['nadopted']}</td>";
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