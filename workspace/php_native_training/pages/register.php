<?php
// if has data, likely post request
if (isset($_POST["user_firstname"])) {
    $first_name = $db->sql->real_escape_string($_POST["user_firstname"]);
    $last_name = $db->sql->real_escape_string($_POST["user_lastname"]);
    $password = $db->sql->real_escape_string($_POST["user_password"]);
    $cpassword = $db->sql->real_escape_string($_POST["user_confirm_password"]);


    // if any of the fields dont have values, return error!
    if (!$first_name || !$last_name || !$password || !$cpassword) {
        echo "you have a missing field";
        die();
    }

    // if passwords
    if ($password != $cpassword) {
        echo "passwords do not match";
        die();
    }

    // encrypt the password
    $password = password_hash($password, PASSWORD_DEFAULT);
    $did_register = $db->sql->query("insert into users
        (
            status,
            first_name,
            last_name,
            password
        )
        values
        (
            1,
            '{$first_name}',
            '{$last_name}',
            '{$password}'
        )");
    
    if ($did_register) {
        $result = $db->sql->query(
            "select * from users where id = {$db->sql->insert_id}"
        );
        $user = $result->fetch_all(MYSQLI_ASSOC);

        // set login to true
        $_SESSION["is_logged_in"] = true;
        $_SESSION["user_id"] = $user[0]['id'];
        $_SESSION["first_name"] = $user[0]['first_name'];
        $_SESSION["last_name"] = $user[0]['last_name'];
        $_SESSION["last_login_time"] = time();
           
        // redirect to another page
        echo "<script>
            window.location.href = '?page=home';
        </script>";
        die();
    }
}
?>
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Registration Form</h3>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="name">FirstName</label>
                            <input type="text" name="user_firstname" class="form-control" value="<?php echo @$_POST['user_firstname']; ?>">

                        </div>
                        <div class="form-group">
                            <label for="name">LastName</label>
                            <input type="text" name="user_lastname" class="form-control" value="<?php echo @$_POST['user_lastname']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="user_password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password">Confirm</label>
                            <input type="password" name="user_confirm_password" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="submit" name="submit" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
