<!DOCTYPE html>
<html>
<head>
    <title><?php echo h($title_for_layout); ?></title>
    <?php echo $this->Html->css('/bootstrap/css/styles.css'); ?>
    <?php echo $this->Html->css('/css/select2.min.css'); ?>
    <?php echo $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css'); ?>
    <?php echo $this->Html->css('/datepicker/datepicker.min.css'); ?>
    <?php echo $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css'); ?>
    <?php echo $this->Html->script('/js/jquery.min.js'); ?>
    <?php echo $this->Html->script('/js/select2.min.js'); ?>
    <?php echo $this->Html->script('/sweetalert/sweetalert.min.js'); ?>
    <?php echo $this->Html->script('/datepicker/datepicker.min.js'); ?>
    <?php echo $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js'); ?>
   <script>
    toastr.options = {
  "closeButton": false,
  "debug": false,
  "newestOnTop": false,
  "progressBar": false,
  "positionClass": "toast-bottom-right",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "5000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
}
   </script>
</head>
<body>
<div class="container">
  <?php if(isset($user)) { ?>
    <header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
      <a href="/cakephp/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
        <svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"/></svg>
        <h1 class="h3 mb-3">Messages Board</h1>
      </a>

      <ul class="nav nav-pills">
        <li class="nav-item">
          <a href="/cakephp/users/profile/<?= AuthComponent::user('id'); ?>" class="nav-link d-flex gap-2"> 
          <?php
          $imagepath = $user['User']['imagepath'] != '' ? $user['User']['imagepath'] : 'uploads/no-image.png';
          echo $this->Html->image('../'. $imagepath, [
                    'class' => 'rounded-circle',
                    'width' => '30',
                    'height' => '30'
                ]); ?>
          <?= AuthComponent::user('name'); ?></a>
        </li>
        <li class="nav-item">
            <?php
            echo $this->Html->link(
                $this->Html->tag('em', '', ['class' => 'icon ni ni-power text-dark', 'escape' => true]) . ' Logout', // Combine icon and text
                ['controller' => 'Users', 'action' => 'logout'],
                ['escape' => false, 'class' => 'nav-link logout-link'] // Apply class directly here
            );
            ?>
        </li>
      </ul>
    </header>
    <?php }?>
  </div>
<?php echo $this->Flash->render(); ?>
    <div class="container">
        <?php echo $this->fetch('content'); ?>
    </div>
</body>
</html>
