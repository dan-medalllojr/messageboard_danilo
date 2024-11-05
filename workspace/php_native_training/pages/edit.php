<?php

if (isset($_POST['id'])) {
    $result = $db->sql->query("
        update contacts 
        set
            name = '{$_POST["first_name"]}',
            company_name = '{$_POST["company_name"]}',
            phone_number = '{$_POST["phone"]}',
            email = '{$_POST["email"]}',
            modified = CURRENT_TIMESTAMP(),
            modified_url = '{$_SERVER['REQUEST_URI']}',
            modified_ip = '{$_SERVER['REMOTE_ADDR']}'
        where 
            id = {$_POST['id']}
    ");
    
    // redirect to another page
	echo "<script>
        window.location.href = '?page=home&success_edit_contact=1';
    </script>";
    die();
}

$result = $db->sql->query("
    select * from contacts 
    where 
        id = {$_GET["id"]}
        and status = 1
");

$contact = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="container">
	<h2>Add Contact</h2>
	<form action="" method="POST">
		<div class="form-group">
			<label for="first_name">Name:</label>
			<input type="text" class="form-control" name="first_name" placeholder="Enter First Name" value="<?php echo @$contact[0]['name']; ?>">
		</div>
		<div class="form-group
			<label for="company_name">Company Name:</label>
			<input type="text" class="form-control" name="company_name" placeholder="Enter Company Name" value="<?php echo @$contact[0]['company_name']; ?>">
		</div>
		<div class="form-group
			<label for="phone">Phone:</label>
			<input type="phone" class="form-control" name="phone" placeholder="Enter Phone" value="<?php echo @$contact[0]['phone_number']; ?>">
		</div>
		<div class="form-group
			<label for="phone">Email:</label>
			<input type="email" class="form-control" name="email" placeholder="Enter Email" value="<?php echo @$contact[0]['email']; ?>">
		</div>
		<input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>" />
		<input type="hidden" name="status" value="1" />
		<input type="hidden" name="id" value="<?php echo @$contact[0]['id']; ?>"/>
		<button type="submit" class="btn btn-primary">Add Contact</button>
	</form>
</div>