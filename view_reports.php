<?php include("lib/header.php"); ?>

<title>View Reports</title>
</head>

<body>
    <div id="main_container">
        <?php include("lib/menu.php"); ?>

        <div class="center_content">
            <div class="center_left">
                <div class="title_name">View Reports</div>
                <div class="features">
                    <div class="section">
                        <div class="subtitle">Animal Control Report</div>
                        <a href='animal_control.php'>View</a>
                    </div>
                    <div class="section">
                        <div class="subtitle">Volunteer of the Month</div>
                        <a href='monthly_volunteer.php'>View</a>
                    </div>
                    <div class="section">
                        <div class="subtitle">Monthly Adoption Report</div>
                        <a href='monthly_adoption.php'>View</a>
                    </div>
                    <div class="section">
                        <div class="subtitle">Volunteer Lookup</div>
                        <a href='volunteer_lookup.php'>View</a>
                    </div>
                    <div class="section">
                        <div class="subtitle">Vaccine Reminder Report</div>
                        <a href='vaccine_reminder.php'>View</a>
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