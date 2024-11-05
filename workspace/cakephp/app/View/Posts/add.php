<h1>Add Post</h1>
<?php
// echo $this->Form->create('Post');
// echo $this->Form->input('title');
// echo $this->Form->input('body', array('rows' => '3'));
// echo $this->Form->end('Save Post');
?>

<form action="" method="POST">
    <input type="text" name="post_title">
    <textarea name="post_body"></textarea>
    <input type="submit" value="Save Post">
</form>