<?php

include('lib/common.php');
// written by GTusername4

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$date = date('m/d/Y');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $firstname = mysqli_real_escape_string($db, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($db, $_POST['lastname']);
    $cofirstname = mysqli_real_escape_string($db, $_POST['cofirstname']);
    $colastname = mysqli_real_escape_string($db, $_POST['colastname']);
    $street = mysqli_real_escape_string($db, $_POST['street']);
    $city = mysqli_real_escape_string($db, $_POST['city']);
    $state = mysqli_real_escape_string($db, $_POST['state']);
    $zipcode = mysqli_real_escape_string($db, $_POST['zipcode']);
    $phonenum = mysqli_real_escape_string($db, $_POST['phonenum']);
    $email = mysqli_real_escape_string($db, $_POST['email']);

    if (empty($firstname) or empty($lastname)) {
        array_push($error_msg,  "Please enter applicant's full name.");
    }

    if (empty($street) or empty($city) or empty($state) or empty($zipcode)) {
        array_push($error_msg,  "Please complete the address.");
    }

    if (empty($email)) {
        array_push($error_msg,  "Please enter applicant's Email.");
    }

    if (empty($phonenum)) {
        array_push($error_msg,  "Please enter applicant's phone number.");
    }

    if (!empty($firstname) && !empty($street) && !empty($email) && !empty($phonenum)) {
        $query = "INSERT INTO adopter (Email, FirstName, LastName, PhoneNum, Street, City, State, ZipCode, CoApplicantFirstName, CoApplicantLastName) " .
            "VALUES ('$email','$firstname','$lastname','$phonenum','$street','$city','$state','$zipcode','$cofirstname','$colastname')";

        // INSERT INTO adopter (Email, FirstName, LastName, PhoneNum, Street, City, State, ZipCode, CoApplicantFirstName, CoApplicantLastName) 
        // VALUES ('$email','$firstname','$lastname','$phonenum','$street','$city','$state','$zipcode','$cofirstname','$colastname')

        $result = mysqli_query($db, $query);
        include('lib/show_queries.php');

        if (mysqli_affected_rows($db) == -1) {
            array_push($error_msg,  "INSERT ERROR: adoptor... <br>" .  __FILE__ . " line:" . __LINE__);
        }

        $query = "INSERT INTO APPLICATION (ApplicationStatus, Email, Date) " .
            "VALUES ('pending','$email','$date')";

        // INSERT INTO APPLICATION (ApplicationStatus, Email, Date) 
        // VALUES ('pending','$email','$date')


        $result = mysqli_query($db, $query);
        $id = mysqli_insert_id($db);

        include('lib/show_queries.php');
        if ($result  == False) {
            array_push($error_msg, "User Interest '" . $interest .  "'  already an interest... <br>" .  __FILE__ . " line:" . __LINE__);
        }
    }
}


?>

<?php include("lib/header.php"); ?>

<title>Add Adoption Application</title>
</head>

<body>
    <div id="main_container">
        <?php include("lib/menu.php"); ?>

        <div class="center_content">
            <div class="center_left">
                <div class="title_name">Add Adoption Application</div>
                <div class="features">
                    <div class="section">
                        <div class="subtitle">Enter Applicant's Info</div>
                        <form name="applicationform" action="add_application.php" method="post">
                            <table>
                                <tr>
                                    <td class="item_label">Applicant's First Name</td>
                                    <td>
                                        <input type="text" name="firstname" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Applicant's Last Name</td>
                                    <td>
                                        <input type="text" name="lastname" required>
                                    </td>

                                </tr>
                                <tr>
                                    <td class="item_label">Co-Applicant's First Name</td>
                                    <td>
                                        <input type="text" name="cofirstname">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Co-Applicant's Last Name</td>
                                    <td>
                                        <input type="text" name="colastname">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Street Address</td>
                                    <td>
                                        <input type="text" name="street" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">City</td>
                                    <td>
                                        <input type="text" name="city" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">State</td>
                                    <td>
                                        <select name="state" required>
                                            <option value="AL">Alabama</option>
                                            <option value="AK">Alaska</option>
                                            <option value="AZ">Arizona</option>
                                            <option value="AR">Arkansas</option>
                                            <option value="CA">California</option>
                                            <option value="CO">Colorado</option>
                                            <option value="CT">Connecticut</option>
                                            <option value="DE">Delaware</option>
                                            <option value="DC">District of Columbia</option>
                                            <option value="FL">Florida</option>
                                            <option value="GA">Georgia</option>
                                            <option value="HI">Hawaii</option>
                                            <option value="ID">Idaho</option>
                                            <option value="IL">Illinois</option>
                                            <option value="IN">Indiana</option>
                                            <option value="IA">Iowa</option>
                                            <option value="KS">Kansas</option>
                                            <option value="KY">Kentucky</option>
                                            <option value="LA">Louisiana</option>
                                            <option value="ME">Maine</option>
                                            <option value="MD">Maryland</option>
                                            <option value="MA">Massachusetts</option>
                                            <option value="MI">Michigan</option>
                                            <option value="MN">Minnesota</option>
                                            <option value="MS">Mississippi</option>
                                            <option value="MO">Missouri</option>
                                            <option value="MT">Montana</option>
                                            <option value="NE">Nebraska</option>
                                            <option value="NV">Nevada</option>
                                            <option value="NH">New Hampshire</option>
                                            <option value="NJ">New Jersey</option>
                                            <option value="NM">New Mexico</option>
                                            <option value="NY">New York</option>
                                            <option value="NC">North Carolina</option>
                                            <option value="ND">North Dakota</option>
                                            <option value="OH">Ohio</option>
                                            <option value="OK">Oklahoma</option>
                                            <option value="OR">Oregon</option>
                                            <option value="PA">Pennsylvania</option>
                                            <option value="RI">Rhode Island</option>
                                            <option value="SC">South Carolina</option>
                                            <option value="SD">South Dakota</option>
                                            <option value="TN">Tennessee</option>
                                            <option value="TX">Texas</option>
                                            <option value="UT">Utah</option>
                                            <option value="VT">Vermont</option>
                                            <option value="VA">Virginia</option>
                                            <option value="WA">Washington</option>
                                            <option value="WV">West Virginia</option>
                                            <option value="WI">Wisconsin</option>
                                            <option value="WY">Wyoming</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">ZipCode</td>
                                    <td>
                                        <input type="text" name="zipcode" maxlength=5 required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Phone Number</td>
                                    <td>
                                        <input type="tel" name="phonenum" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Email</td>
                                    <td>
                                        <input type="email" name="email" required>
                                    </td>
                                </tr>
                            </table>

                            <a href="javascript:applicationform.submit();" class="fancy_button">Submit</a>

                        </form>

                    </div>

                    <div class="section">
                        <?php
                        if (!empty($id)) {
                            print "'The assigned application number is $id";
                        }
                        ?>

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