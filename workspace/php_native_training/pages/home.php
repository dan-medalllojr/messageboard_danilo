<?php

// for creating contact
if (isset($_POST["first_name"])) {
    $first_name = $db->sql->real_escape_string($_POST["first_name"]);
    $company_name = $db->sql->real_escape_string($_POST["company_name"]);
    $phone = $db->sql->real_escape_string($_POST["phone"]);
    $email = $db->sql->real_escape_string($_POST["email"]);
    $status = $db->sql->real_escape_string($_POST["status"]);
    $user_id = $db->sql->real_escape_string($_POST["user_id"]);

    $did_add_contact = $db->sql->query("insert into contacts
        (
            status,
            user_id,
            name,
            company_name,
			email,
			phone_number
        )
        values
        (
            '{$status}',
            '{$user_id}',
            '{$company_name}',
            '{$first_name}',
            '{$email}',
            '{$phone}'
        )");
		
		// redirect to another page
		echo "<script>
			window.location.href = '?page=home&success_add_contact=1';
		</script>";
		die();
}

// for delete contact
if (isset($_POST["action_type"]) && $_POST["action_type"] == "delete") {
	$did_delete_contact = $db->sql->query("
		update contacts
			set status = 0
			where id = {$_POST['id']};");
		
	// redirect to another page
	echo "<script>
		window.location.href = '?page=home&success_delete_contact=1';
	</script>";
	die();
}

$result = $db->sql->query("
    select * from contacts 
    where 
        user_id = {$_SESSION["user_id"]}
		and status = 1
");

if (isset($_GET["success_add_contact"])) {
	echo "successfully added contact!";
}
?>

<?php
// - if no data
if ($result->num_rows <= 0):
?>
	<table class="table table-bordered">
		<tr>
			<td>no data</td>
		</tr>
	</table>
<?php

// - if has data
else:
	$contacts = $result->fetch_all(MYSQLI_ASSOC);
?>
	<table class="table table-bordered">
		<tr>
			<th>#</th>
			<th>Name</th>
			<th>Company Name</th>
			<th>Email</th>
			<th>Phone</th>
			<th></th>
		</tr>
		<?php foreach($contacts as $contact): ?>
		<tr>
			<td><?php echo $contact["id"]; ?></td>
			<td><?php echo $contact["name"]; ?></td>
			<td><?php echo $contact["company_name"]; ?></td>
			<td><?php echo $contact["email"]; ?></td>
			<td><?php echo $contact["phone_number"]; ?></td>
			<td>
				<!-- for delete -->
				<form action="?page=home" method="POST">
					<input type="hidden" name="action_type" value="delete" />
					<input type="hidden" name="id" value="<?php echo $contact["id"]; ?>" />
					<input type="submit" value="Delete" class="btn btn-danger" />
				</form>

				<!-- for edit -->
				<form action="" method="GET">
					<input type="hidden" name="page" value="edit" />
					<input type="hidden" name="action_type" value="edit" />
					<input type="hidden" name="id" value="<?php echo $contact["id"]; ?>" />
					<input type="submit" value="Edit" class="btn btn-primary" />
				</form>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php
endif;
?>

<div class="container">
	<h2>Add Contact</h2>
	<form action="" method="POST">
		<div class="form-group">
			<label for="first_name">Name:</label>
			<input type="text" class="form-control" name="first_name" placeholder="Enter First Name">
		</div>
		<div class="form-group
			<label for="company_name">Company Name:</label>
			<input type="text" class="form-control" name="company_name" placeholder="Enter Company Name">
		</div>
		<div class="form-group
			<label for="phone">Phone:</label>
			<input type="phone" class="form-control" name="phone" placeholder="Enter Phone">
		</div>
		<div class="form-group
			<label for="phone">Email:</label>
			<input type="email" class="form-control" name="email" placeholder="Enter Email">
		</div>
		<input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>" />
		<input type="hidden" name="status" value="1" />
		<button type="submit" class="btn btn-primary">Add Contact</button>
	</form>
</div>