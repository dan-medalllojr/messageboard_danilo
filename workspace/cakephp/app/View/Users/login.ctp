<div class="container vh-100">
    <div class="row justify-content-center align-items-center h-100">
        <div class="col-md-7 col-lg-5">
            <div class="login-wrap p-4 p-md-5">
                <div class="d-flex">
                    <div class="w-100">
                        <h3 class="mb-4">Sign In</h3>
                    </div>
                </div>
                <div id="login-result"></div>
                <?= $this->Flash->render() ?>
                <?= $this->Form->create('User', ['id' => 'login-form', 'url' => ['action' => 'login'], 'class' => 'login-form']); ?>
                    <div class="form-group">
                        <?= $this->Form->input('email', [
                            'label' => false,
                            'placeholder' => 'Email',
                            'class' => 'form-control rounded-left mb-2',
                            'required' => true
                        ]); ?>
                    </div>
                    <div class="form-group">
                        <?= $this->Form->input('password', [
                            'label' => false,
                            'placeholder' => 'Password',
                            'class' => 'form-control rounded-left mb-2',
                            'type' => 'password',
                            'required' => true
                        ]); ?>
                    </div>
                    <div class="form-group d-flex align-items-center">
                        <div class="w-100">
                            <div class="w-100 d-flex">
                                <?= $this->Form->button(__('Login'), ['class' => 'btn btn-primary rounded w-100 submit']); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-4">
                        <div class="w-100 text-center">
                            <p class="mb-1">Don't have an account? <a href="<?= $this->Html->url(['controller' => 'Users', 'action' => 'add']); ?>">Sign Up</a></p>
                        </div>
	                </div>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#login-form').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            $.ajax({
                url: $(this).attr('action'), // The form action
                type: "POST",
                data: $(this).serialize(),
                dataType: 'json', // Expect JSON response
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect; // Redirect on success
                    } else {
                        $('#login-result').html('<div class="alert alert-danger">' + response.message + '</div>');
                        $('#login-result').fadeIn(1000);
                        $('#login-result').fadeOut(3000);
                    }
                }
            });
        });
    });
</script>
