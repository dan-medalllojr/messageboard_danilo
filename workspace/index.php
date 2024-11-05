<?php
// initialize any config files
require_once "config/init.php";

// $car1 = new Car("v6", 4, false, 20, 5);

// $car2 = new Car("v8", 4, false, 100, 2);

// // convoy
// $moalboal_distaince = 85;
// $total_people = 20;

// echo $car1->variable1;

// $car1->travel("moalboal", 85);
// echo "<hr/>";

// $car2->travel("moalboal", 85);
// echo "<hr/>";

// $car1->calculate_travel_cycles($total_people);
// echo "<hr/>";
// $car2->calculate_travel_cycles($total_people);
// $wigo = new Toyota("v3", 4, false, 60, 5, 100);
// $xpander = new Nissan();
// $coolray = new Geely();

// echo "<pre>";
// $wigo->calculateMaintenceCost();
// // $wigo->doAction();

// die();


// load header
include "view_partials/header.php";

if (array_key_exists('page', $_GET)) {
	switch ($_GET['page']) {
		case 'register':
			if (isset($_SESSION['is_logged_in'])) {
				include "view_partials/forbidden_logout.php";

			} else {
				include "pages/register.php";
			}
			break;

		case 'login':
			if (isset($_SESSION['is_logged_in'])) {
				include "view_partials/forbidden_logout.php";
			} else {
				include "pages/login.php";
			}
			break;
		
		case 'edit':
			include "pages/edit.php";
			break;
			
		case 'logout':
			// delete session
			session_destroy();

			// redirect to login
			echo "<script>
				window.location.href = '?page=login&debug_came_from_logout=1';
			</script>";
			break;
		
		case "home":
			if (!isset($_SESSION['is_logged_in'])) {
				include "view_partials/forbidden_login.php";
				
			} else {
				include "pages/home.php";
			}
			break;
		default:
			if (!isset($_SESSION['is_logged_in'])) {
				// redirect to login
				echo "<script>
					window.location.href = '?page=login&debug_came_from_logout=1';
				</script>";
				
			} else {
				// redirect to login
				echo "<script>
					window.location.href = '?page=home&debug_came_from_logout=1';
				</script>";
			}
			break;
	}
} else {
	if (!isset($_SESSION['is_logged_in'])) {
		// redirect to login
		echo "<script>
			window.location.href = '?page=login&debug_came_from_logout=1';
		</script>";
		
	} else {
		// redirect to login
		echo "<script>
			window.location.href = '?page=home&debug_came_from_logout=1';
		</script>";
	}
}

// load footer
include "view_partials/footer.php";