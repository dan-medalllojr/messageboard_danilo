<?php
ECHO "<pre>";
var_dump($_POST); // files will not be included here
var_dump($_FILES); // binary data or files will be in here


$did_upload = move_uploaded_file($_FILES["fdci_image"]["tmp_name"], "public/{$_FILES['fdci_image']['name']}");
if (!$did_upload) {
	$usr_image = "random_avatar_image"
}
var_dump($did_upload);
echo "</pre>";
?>
<form method="POST" action="" enctype="multipart/form-data">
	<input type="text" name="firstname" />
	<input type="file" name="fdci_image" />
	<input type="submit" name="Edit">
</form>