<section class="h-100 gradient-custom-2">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center">
      <div class="col col-lg-9 col-xl-8">
        <div class="card">
          <div class="rounded-top text-white d-flex flex-row" style="background-color: #000; height:200px;">
            <div class="ms-4 mt-5 d-flex flex-column gap-2" style="width: 150px;">
                <?php
                  $imagepath = $user['User']['imagepath'] != '' ? $user['User']['imagepath'] : 'uploads/no-image.png';
                echo $this->Html->image('../'.$imagepath, [
                    'class' => 'mg-fluid img-thumbnail mt-4 mb-2',
                    'id' => 'imagePreview',
                    'style' => 'width: 150px; z-index: 1;'
                ]); ?>
              <a  href="/cakephp/edit/<?= AuthComponent::user('id'); ?>" class="btn btn-outline-primary" style="z-index: 1;" <?= h($user['User']['id']) != AuthComponent::user('id') ? 'hidden' : '' ?>>
                Edit profile
              </a>
            </div>
            <div class="ms-3" style="margin-top: 130px;">
              <h5><?= h($user['User']['name']); ?></h5>
              <p><?= h($user['User']['gender']) == 1 ? 'Male' : 'Female'; ?></p>
            </div>
          </div>
          <div class="p-4">
            <div class="d-flex justify-content-end gap-4 text-center py-1 text-body">
            <div>
                <h5 class="mb-1">Birthdate</h5>
                <p class="small text-muted mb-0"><?= date('F d, Y', strtotime(h($user['User']['birth_date']))); ?></p>
              </div>
              <div>
                <h5 class="mb-1">Joined</h5>
                <p class="small text-muted mb-0"><?= date('F d, Y', strtotime(h($user['User']['created']))); ?></p>
              </div>
              <div class="px-3">
                <h5 class="mb-1">Last Login</h5>
                <p class="small text-muted mb-0"><?= date('F d, Y h:i:s A', strtotime(h($user['User']['user_login_time']))); ?></p>
              </div>
            </div>
          </div>
          <div class="card-body p-4 text-black">
            <div class="mb-5  text-body">
              <p class="lead fw-normal mb-1">Hubby</p>
              <div class="p-4 bg-dark text-light">
                <p class="font-italic"><?= h($user['User']['hubby']) ?></p>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>