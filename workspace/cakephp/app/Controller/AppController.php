<?php
App::uses('Controller', 'Controller');

class AppController extends Controller {
    public $components = ['Session', 'Auth'];

    public function beforeFilter() {
        parent::beforeFilter();

        // Access the logged-in user's data
        $user = $this->Auth->user(); // This returns the user's data array

        // You can set the user data to be available in the view
        $this->set('user', $user); 
        

        // Configure Auth component
        $this->Auth->authenticate = [
            'Form' => [
                'fields' => [
                    'username' => 'email', // Assuming you're using email as username
                    'password' => 'password'
                ],
                'passwordHasher' => 'Blowfish'
            ]
        ];

        // Set the login and logout redirect URLs
        $this->Auth->loginRedirect = ['controller' => 'Messages', 'action' => 'index'];
        $this->Auth->logoutRedirect = ['controller' => 'Users', 'action' => 'login'];
        
        $this->Auth->allow('login','add'); // Allow public access to login actions
        if(in_array($this->request->here, array('/cakephp/login', '/cakephp/add')) && $this->Auth->user()) {
            return $this->redirect(array('controller' => 'users', 'action' => 'index'));
        }
    }
}
