<?php
App::uses('Security', 'Utility');
App::uses('Log', 'Log');
class UsersController extends AppController {
    public $components = ['Auth', 'Session'];

    public function login() {
        // Check if it's an AJAX request
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            if ($this->Auth->login()) {
                $userId = $this->Auth->user('id');
    
                if ($userId) {
                    $this->User->id = $userId;
                    $this->User->saveField('user_login_time', date('Y-m-d H:i:s'));
                }
    
                $response = [
                    'success' => true,
                    'redirect' => Router::url(['controller'=>'Messages','action' => 'index'])
                ];
            } else {
                // Invalid credentials for AJAX
                $response = [
                    'success' => false,
                    'message' => __('Invalid email or password, try again.')
                ];
            }
    
            $this->response->type('application/json');
            $this->response->body(json_encode($response));
            return $this->response;
        }
    
        // Set the title for the layout
        $this->set('title_for_layout', 'Login');
    }
    

    public function add() {
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->User->create();
            $this->request->data['User']['status'] = 1;
            $this->request->data['User']['created_ip'] = $this->request->clientIp();
            $this->request->data['User']['user_login_time'] = date('Y-m-d H:i:s');
            
            // Set the user data
            $this->User->set($this->request->data);
            
            $response = [];
    
            // Validate and save the user data
            if ($this->User->validates()) {
                if ($this->User->save($this->request->data)) {
                    // Attempt to log the user in
                    if ($this->Auth->login($this->User->data)) {
                        $response = [
                            'success' => true, 
                            'redirect' => Router::url(['action' => 'thankYou'])
                        ];
                    } else {
                        $response = ['success' => false, 'message' => __('Login failed. Please try again.')];
                    }
                } else {
                    $response = ['success' => false, 'message' => __('Registration failed. Please try again.')];
                }
            } else {
                $response = ['success' => false, 'message' => $this->User->validationErrors];
            }
    
            $this->response->type('application/json');
            $this->response->body(json_encode($response));
            return $this->response; // Return the response object
        }
        
        $this->set('title_for_layout', 'Registration');
    }
    

    //profile
    public function profile($id = null){

          // If no `id` is provided, throw a 404 error
          if (!$id) {
            throw new NotFoundException(__('Invalid user ID'));
        }
        
        // Fetch the user by ID from the database
        $user = $this->User->findById($id);
        
        // If the user does not exist, throw a 404 error
        if (!$user) {
            throw new NotFoundException(__('User not found'));
        }
        
        
        // Pass the user data to the view
        $this->set('user', $user);
        $this->set('title_for_layout', $user['User']['name'] . "'s Profile");

    }

    public function logout() {
        // Clear the session data
        $this->Session->destroy(); // This will delete all session data
        return $this->redirect($this->Auth->logout());
    }

    public function uploadImage() {
        $this->autoRender = false; // Disable the default view rendering
    
        if ($this->request->is('post')) {
            // Check if an image file was uploaded
            if (!empty($_FILES['image']['name'])) {
                $file = $_FILES['image'];
    
                // Log the uploaded file data
                $this->log('debug', 'Uploaded file data: ' . print_r($file, true));
    
                // Check for upload errors
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    return json_encode(['success' => false, 'message' => 'File upload error: ' . $file['error']]);
                }
    
                // Define allowed file types
                $allowedTypes = ['image/gif', 'image/jpeg', 'image/jpg', 'image/png'];
                $fileType = '';
    
                // Ensure tmp_name is set before calling mime_content_type
                if (!empty($file['tmp_name'])) {
                    $fileType = mime_content_type($file['tmp_name']);
                } else {
                    return json_encode(['success' => false, 'message' => 'Temporary file not found.']);
                }
    
                $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
                // Check if the file type is allowed
                if (!in_array($fileType, $allowedTypes)) {
                    return json_encode(['success' => false, 'message' => 'Invalid file type. Only GIF, JPEG, JPG, and PNG files are allowed.']);
                }
    
                // Ensure the uploads directory exists
                $uploadPath = WWW_ROOT . 'uploads' . DS;
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
    
                // Generate a unique file name to prevent overwrites
                $uploadFile = $uploadPath . uniqid('', true) . '.' . $fileExtension;
    
                // Move the uploaded file to the specified directory
                if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                    $userId = $this->Auth->user('id');
                    if ($userId) {
                        $imagePath = 'uploads/' . basename($uploadFile);
                        $this->loadModel('Users'); // Ensure the model is loaded correctly
    
                        if ($this->User->exists($userId)) {
                            // Prepare data for update
                            $data = [
                                'id' => $userId,
                                'imagepath' => $imagePath
                            ];
    
                            // Attempt to save the data
                            $this->log('debug', 'Saving user data: ' . print_r($data, true));
                            if ($this->User->save($data)) {
                                return json_encode(['success' => true, 'message' => 'Photo successfully updated.']);
                            } else {
                                return json_encode(['success' => false, 'message' => 'Failed to save image path in database.']);
                            }
                        } else {
                            return json_encode(['success' => false, 'message' => 'User not found.']);
                        }
                    } else {
                        return json_encode(['success' => false, 'message' => 'User not logged in.']);
                    }
                } else {
                    return json_encode(['success' => false, 'message' => 'File upload failed.']);
                }
            } else {
                return json_encode(['success' => false, 'message' => 'No file uploaded.']);
            }
        }
    
        return json_encode(['success' => false, 'message' => 'Invalid request.']);
    }
    

    public function edit($id = null) {
        // Check if the user is logged in
        if (!$this->Auth->user()) {
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        $loggedInUserId = $this->Auth->user('id');
        if ($loggedInUserId != $id) {
            $this->Auth->logout();
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }
    
        $this->User->id = $id;
    
        if ($this->request->is('put')) {
            $this->autoRender = false;
            $this->User->set($this->request->data);
  
            // Handle password update logic
            if (empty($this->request->data['password'])) {
                unset($this->request->data['password']); // Do not update the password if it's empty
            } 

            $this->request->data['gender'] = $this->request->data['gender'];
            $this->request->data['modified_ip'] = $this->request->clientIp();  // Get the client's IP address

            if ($this->User->validates()) {
                if ($this->User->save($this->request->data)) {
                    $response = [
                        'status' => 'success',
                        'message' => 'User updated successfully.',
                        'user' => $this->User->findById($id)
                    ];
                    $this->response->type('application/json');
                    $this->response->body(json_encode($response));
                    $this->set('user', $response['user']);
                    return $this->response;
                } else {
                    $this->log($this->User->validationErrors, 'debug'); // Log validation errors
                    $response = [
                        'status' => 'error',
                        'message' => 'Unable to update user.'
                    ];
                    $this->response->type('application/json');
                    $this->response->body(json_encode($response));
                    return $this->response;
                }
            } else {
                $this->log($this->User->validationErrors, 'debug'); // Log validation errors
                $response = [
                    'status' => 'error',
                    'message' => 'Validation errors.',
                    'errors' => $this->User->validationErrors
                ];
                $this->response->type('application/json');
                $this->response->body(json_encode($response));
                return $this->response;
            }
        }
    
        $user = $this->User->findById($id);
        $this->set('user', $user);

        $this->set('title_for_layout', 'Edit Profile');
    }
    
    
    

    public function thankYou(){
         // Set the title for the layout
        $this->set('user', 'Registered');
        $user = $this->User->findById($this->Auth->user('id'));
        $this->set('user', $user);
    }
    
    
}
?>
