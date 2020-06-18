
			<!-- <div id="header">
                <div class="logo"><img src="img/gtonline_logo.png" style="opacity:0.6;background-color:E9E5E2;" border="0" alt="" title="GT Online Logo"/></div>
			</div> -->
			
			<div class="nav_bar">
				<ul>    
                    <li><a href="dashboard.php" <?php if($current_filename=='dashboard.php') echo "class='active'"; ?>>Dashboard</a></li>                       
					<?php
					    if ($_SESSION['role'] == 'Admin') {
					        print "<li><a href='view_reports.php'";
					        if($current_filename=='view_reports.php') {echo "class='active'";}
					        print ">View Reports</a></li>";
					        }
					?>
                    <li><a href="add_application.php" <?php if(strpos($current_filename, 'add_application.php') !== false) echo "class='active'"; ?>>Add Adoption Application</a></li>  
                    <?php
                        if ($_SESSION['role'] == 'Admin') {
                            print "<li><a href='view_application.php'";
                            if($current_filename=='view_application.php') {echo "class='active'";}
                            print ">View Adoption Application</a></li>";
                        }
                    ?>
                    <li><a href="logout.php"> <span class='glyphicon glyphicon-log-out'></span> Log Out</a></li>              
				</ul>
			</div>