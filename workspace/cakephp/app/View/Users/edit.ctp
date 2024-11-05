<div class="container py-2 h-100">
  <div class="row d-flex justify-content-center align-items-center h-100">
    <div class="col col-lg-6 mb-4 mb-lg-0">
      <div id="result"></div>
      <div class="card mb-3" style="border-radius: .5rem;">
        <div class="row g-0">
          <div class="col-md-4 gradient-custom text-center text-white" style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
            <?php
            $imagepath = $user['User']['imagepath'] != '' ? $user['User']['imagepath'] : 'uploads/no-image.png';
            echo $this->Html->image('../' . $imagepath, [
              'class' => 'img-fluid my-5 rounded-circle',
              'id' => 'imagePreview',
              'style' => 'max-width: 200px; margin-top: 10px;'
            ]); ?>
            <div>
              <div class="d-flex justify-content-center">
                <form id="imageUploadForm" enctype="multipart/form-data">
                  <input type="file" id="imageUpload" name="image" accept=".jpg, .jpeg, .png, .gif" hidden>
                  <div class="btn btn-secondary btn-sm" id="upload">Upload photo</div>
                  <div class="btn btn-primary btn-sm" id="updateButton">Update Image</div>
                </form>
              </div>
            </div>
          </div>
          <div class="col-md-8">
            <div class="card-body p-4">
              <h6>Information</h6>
              <hr class="mt-0 mb-4">
              <div class="row pt-1">
                <div class="col-6 mb-3">
                  <h6>Name</h6>
                  <input type="text" name="name" value="<?= h($user['User']['name']); ?>" id="name" class="form-control text-muted">
                </div>
                <div class="col-6 mb-3">
                  <h6>Birthdate</h6>
                  <input type="text" name="birth_date" value="<?= date('m/d/Y', strtotime(h($user['User']['birth_date']))); ?>" id="birth_date" class="form-control datepicker text-muted">
                </div>
              </div>
              <hr class="mt-0 mb-4">
              <div class="row pt-1">
                <div class="col-6 mb-3">
                  <h6 class="pb-1">Email</h6>
                  <input type="text" name="email" value="<?= h($user['User']['email']) ?>" id="email" class="form-control text-muted">
                </div>
                <div class="col-6 mb-3">
                  <h6 class="m-0 p-0">Password</h6>
                  <p class="m-0 p-0 text-muted" style="font-size: xx-small;">You can leave as blank</p>
                  <input type="password" name="password" placeholder="Password" id="password" class="form-control">
                </div>
              </div>
              <hr class="mt-0 mb-4">
              <div class="row pt-1">
                <div class="col-6 mb-3">
                  <h6>Gender</h6>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="inlineRadio1" value="1" <?= trim($user['User']['gender']) == 1 ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="inlineRadio1">Male</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="inlineRadio2" value="2" <?= trim($user['User']['gender']) == 2 ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="inlineRadio2">Female</label>
                  </div>
                </div>
                <div class="col-6 mb-3">
                  <h6>Age</h6>
                  <p class="text-muted" id="age"></p>
                </div>
              </div>
              <hr class="mt-0 mb-4">
              <div class="row pt-1 mb-2">
                <textarea name="hubby" id="hubby" class="form-control text-muted" placeholder="Enter your hubby"><?= h($user['User']['hubby']) ?></textarea>
              </div>
              <div class="d-flex justify-content-end">
                <div class="btn btn-sm btn-primary" id="updateBtn">Update</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    getAge();
    $('#updateButton').hide()
    $(".datepicker").datepicker();
    let selectedFile;

    // Preview the selected image
    $('#imageUpload').on('change', function(event) {
      selectedFile = event.target.files[0];

      if (selectedFile) {
        const reader = new FileReader();
        reader.onload = function(e) {
          $('#imagePreview').attr('src', e.target.result).show();
        };
        reader.readAsDataURL(selectedFile);
        $('#upload').hide();
        $('#updateButton').show()
      }
    });

    // Trigger the file input click when "Upload photo" is clicked
    $('#upload').on('click', function() {
      $('#imageUpload').click();
    });

    // Handle the update button click
    $('#updateButton').on('click', function(event) {
      event.preventDefault(); // Prevent default form submission

      if (selectedFile) {
        const formData = new FormData($('#imageUploadForm')[0]); // Get form data

        $.ajax({
          url: '/cakephp/users/uploadImage', // Change to your upload URL
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
            var data = JSON.parse(response);
            if (data.success) {
              $('#result').html('<div class="alert alert-success">' + data.message + '</div>');
            } else {
              $('#result').html('<div class="alert alert-success">' + data.errors + '</div>');
            }
            $('#result').fadeOut(3000);
            $('#updateButton').hide()
            $('#upload').show();
          },
          error: function(error) {
            alert('An error occurred while uploading the image.');
          }
        });
      } else {
        alert('Please select an image to upload.'); // Alert if no image is selected
      }
    });


    // update
    $('#updateBtn').on('click', function(e) {
      e.preventDefault();
      const name = $('#name').val();
      const email = $('#email').val();
      const hubby = $('#hubby').val().trim();
      const password = $('#password').val();
      const selectedGender = $('input[name="gender"]:checked').val();
      const birthDate = $('#birth_date').val();
      const parts = birthDate.split('/');
      const formattedDate = `${parts[2]}-${parts[0]}-${parts[1]}`; // YYYY-MM-DD
      if(!selectedGender){
        $('#result').html('<div class="alert alert-danger">Please select your gender.</div>');
        return;
      }
      const formData = { // why the gender is not passing value on the payload
        email: email,
        name: name,
        hubby: hubby,
        password: password,
        gender: selectedGender,
        birth_date: formattedDate
      };
      console.log(formData)
      $.ajax({
        type: 'PUT',
        url: '/cakephp/users/edit/<?= h($user['User']['id']) ?>', // Make sure to echo user ID
        data: formData,
        dataType: 'json',
        success: function(res) {
          if (res.status === 'success') {
            $('#result').html('<div class="alert alert-success">' + res.message + '</div>');
            $('#result').fadeIn(1000);
            $('#result').fadeOut(3000);
            setTimeout(() => {
              // window.location.href = '/cakephp/profile';
            }, 2000);
          } else {
            if (res.errors) {
              if (res.errors.email) {
                $('#result').html('<div class="alert alert-danger">' + res.errors.email + '</div>');
              } else if (res.errors.gender) {
                $('#result').html('<div class="alert alert-danger">' + res.errors.gender + '</div>');
              } else if (res.errors.hubby) {
                $('#result').html('<div class="alert alert-danger">' + res.errors.hubby + '</div>');
              } else if (res.errors.birth_date) {
                $('#result').html('<div class="alert alert-danger">' + res.errors.birth_date + '</div>');
              } else {
                $('#result').html('<div class="alert alert-danger">' + res.errors.name + '</div>');
              }
              return;
            }
            $('#result').html('<div class="alert alert-danger">' + res.message + '</div>');

            $('#result').fadeIn(1000);
            $('#result').fadeOut(3000);
          }


        },
        error: function(xhr, status, error) {
          // console.error(xhr.responseText); // Log the response text
          console.log(xhr.responseText); // Log the status code
          //alert('An error occurred. Please try again.');
        }

      });
    });

    $('#birth_date').on('input', function() {
      getAge()
    })

    function getAge() {
      const dob = $('#birth_date').val(); // Get the value from the input

      if (dob) {
        const birthDate = new Date(dob);
        const today = new Date();

        // Calculate age
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
          age--;
        }
        $('#age').text(age);
      } else {
        $('#ageResult').text('Please enter a valid date of birth.');
      }
    }
  });
</script>