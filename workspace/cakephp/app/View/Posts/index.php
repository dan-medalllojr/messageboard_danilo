<!-- File: /app/View/Posts/index.ctp -->

<h1>Blog posts</h1>
<?php echo $this->Html->link(
    'Add Post',
    array('controller' => 'posts', 'action' => 'add')
); ?>
<!-- <a href="/cakephp/posts/add">Add Post</a> -->


<table>
    <tr>
        <th>Id</th>
        <th>Title</th>
        <th>Edit</th>
        <th>Created</th>
    </tr>

    <!-- Here is where we loop through our $posts array, printing out post info -->

    <?php foreach ($posts as $post): ?>
    <tr>
        <td><?php echo $post['Post']['id']; ?></td>
        <td>
            <?php echo 
                $this->Html->link(
                        $post['Post']['title'],
                        array(
                            'controller' => 'posts', 
                            'action' => 'view', 
                            $post['Post']['id']
                        )
                    ); 
            ?>
            <!-- <a href="/cakephp/posts/view/<?php echo $post['Post']['id']; ?>">
                <?php echo $post['Post']['title'] ?>
            </a> -->
        </td>
        <td>
            <?php
                echo $this->Form->postLink(
                    'Delete',
                    array('action' => 'delete', $post['Post']['id']),
                    array('confirm' => 'Are you sure?')
                );
            ?>
            <?php
                echo $this->Html->link(
                    'Edit',
                    array('action' => 'edit', $post['Post']['id'])
                );
            ?>
        </td>

        <td><?php echo $post['Post']['created']; ?></td>
    </tr>
    <?php endforeach; ?>

    <?php echo $this->Paginator->numbers(); ?>
    
    <?php unset($post); ?>

    
</table>