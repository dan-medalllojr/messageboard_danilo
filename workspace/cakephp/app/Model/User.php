<?php
App::uses('AppModel', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {
    public $validate = array(
        'name' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'message' => 'Please enter your name.',
            ),
            'length' => array(
                'rule' => array('lengthBetween', 5, 20),
                'message' => 'Your name must be between 5 and 20 characters long.',
            )
        ),
        'email' => array(
            'notEmpty' => array(
                'rule' => 'notBlank',
                'message' => 'Please enter your email.',
            ),
            'valid' => array(
                'rule' => 'email',
                'message' => 'Please provide a valid email address.'
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'This email address is already in use.'
            )
        ),
        'old_password' => array(
            'notEmpty' => array(
                'rule' => 'notBlank',
                'message' => 'Please enter your old password'
            ),
            'valid' => array(
                'rule' => array('verifyPassword'),
                'message' => 'Old password is incorrect, please try again',
            )
        ),
        'password' => array(
            'rule' => 'notBlank',
            'message' => 'Please enter your password',
            'allowEmpty' => true,
            'required' => false
        ),
        'password_confirmation' => array(
            'rule' => array('confirmed'),
            'message' => 'Password does not match',
        ),
        'birth_date' => array(
            'rule' => 'date',
            'message' => 'Please select a date',
            'on' => 'update'
        ),
        'gender' => array(
            'rule' => 'notBlank',
            'message' => 'Please select a gender',
            'on' => 'update'
        ),
        'hubby' => array(
            'rule' => 'notBlank',
            'message' => 'Please enter your hubbies',
            'on' => 'update'
        ),
        'image' => array(
            'extension' => array(
                'rule' => array('checkExtension', array('jpg', 'png', 'gif')),
                'message' => 'Please upload an image with an extension of (jpg, png, gif)',
                'on' => 'update'
            )
        )
    );

    public function matchPasswords($check) {
        return isset($this->data[$this->alias]['password']) && $check['confirm_password'] === $this->data[$this->alias]['password'];
    }

    public function beforeSave($options = array()) {
        if (isset($this->data[$this->alias]['password'])) {
            $passwordHasher = new BlowfishPasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash(
                $this->data[$this->alias]['password']
            );
        }
    }
}
