<div class="container vh-100">
    <div class="row justify-content-center align-items-center h-100">
        <div class="col-md-7 col-lg-5">
            <div class="login-wrap p-4 p-md-5">
            <div class="d-flex">
                    <div class="w-100">
                        <h3 class="mb-4">Sign Up</h3>
                    </div>
                </div>
                <div class="users form">
                <div id="result"></div>
                <?php echo $this->Flash->render(); ?>
                    <?= $this->Form->create('User', ['id' => 'register-form', 'url' => ['action' => 'add']]); ?>
                        <fieldset>
                            <?= $this->Form->input('name', ['class' => 'form-control rounded-left mb-2','label' => 'Name', 'required' => true]); ?>
                            <?= $this->Form->input('email', ['class' => 'form-control rounded-left mb-2','label' => 'Email', 'required' => true]); ?>
                            <?= $this->Form->input('password', ['class' => 'form-control rounded-left mb-2','label' => 'Password', 'id' => 'password', 'type' => 'password', 'required' => true]); ?>
                            <?= $this->Form->input('confirm_password', ['class' => 'form-control rounded-left mb-2','label' => 'Confirm Password', 'id' => 'confirm-password', 'type' => 'password', 'required' => true]); ?>
                        </fieldset>
                        <div class="w-full d-flex">
                                <?= $this->Form->button(__('Register'), ['class' => 'btn btn-primary rounded w-100 submit']); ?>
                            </div>
                    <?= $this->Form->end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var isMatch = false;
        $('#register-form').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            if(isMatch){
                        $.ajax({
                        url: $(this).attr('action'), // The form action
                        type: 'POST',
                        data: $(this).serialize(),
                        dataType: 'json', // Expect JSON response
                        success: function(response) {
                            console.log(response)
                            if (response.success) {
                                    window.location.href = response.redirect; // Redirect to the URL provided
                             } else {
                                if(response.message.name){
                                    $('#result').html('<div class="alert alert-danger">' + response.message.name + '</div>');
                                }else{
                                    $('#result').html('<div class="alert alert-danger">' + response.message.email + '</div>');
                                }
                                    $('#result').fadeIn(1000);
                                    $('#result').fadeOut(3000);
                                }
                        }
                    });
            }else{
                $('#result').html('<div class="alert alert-danger">Passwords do not match. Please try again.</div>');
                $('#result').fadeIn(1000);
                $('#result').fadeOut(3000);
            }
        });
        
            $('#confirm-password').on('keyup', function() {
            var password = $('#password').val();
            var confirmPassword = $(this).val();
            
            if (password === confirmPassword) {
                $('#result').html('<div class="alert alert-success">Passwords match!</div>');
                isMatch = true;
            } else {
                $('#result').html('<div class="alert alert-danger">Passwords do not match. Please try again.</div>');
            }
        });
        
    });
</script>
