<!-- File: /app/View/Posts/edit.ctp -->

<h1>Edit Post</h1>
<?php
echo $this->Form->create('Post');
echo $this->Form->input('title');
echo $this->Form->input('body', array('rows' => '3'));
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->end('Save Post');
?>

<!-- 
<form action="" method="POST">
    <input type="text" name="post_title" value="<?php ?>">
    <textarea name="post_body"><?php echi ?></textarea>
    <input type="submit" value="Save Post">
</form> -->