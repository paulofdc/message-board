<?php
App::uses('AppModel', 'Model');
/**
 * User Model
 *
 */
class User extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
			'Minimum Length' => [
				'rule' => ['minLength', '5'],
				'message' => 'Please enter at least 5 characters.',
			],
			'Maximum Length' => [
				'rule' => ['maxLength', '20'],
				'message' => 'Please enter not more than 20 characters.',
			]
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Please input valid email address.'
			),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'This email address is already in use.'
            ),
		),
		'password' => array(
			'Password' => array(
				'rule' => array('notBlank'),
			),
			'Match passwords' => [
				'rule' => ['matchPasswords'],
				'message' => 'Your passwords do not match.',
			]
		),
		'confirm_password' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please confirm your password.',
			)
		),
		'current_password' => array(
			'Current Password' => array(
				'rule' => array('notBlank'),
				'message' => 'Current password is required.',
			),
			'Current Password Checker' => [
				'rule' => ['currentPasswordChecker'],
				'message' => 'Your inputted password does not match with the current password.',
			]
		),
		'gender' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'photo' => array(
			'photo' => array(
				'rule' => array('notBlank'),
				'required' => false,
				'allowEmpty' => true,
			),
		),
		'birthdate' => array(
			'date' => array(
				'rule' => array('date'),
				'message' => 'Birthdate is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'last_login' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				'required' => false,
				'allowEmpty' => true,
			),
		),
		'other_details' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Other Details is required.',
			),
		),

		'current_email_address' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Please input valid email address.'
			),
			'Current Email Required' => array(
				'rule' => array('notBlank'),
				'message' => 'Current Email is required.',
			),
			'Current Email Checker' => [
				'rule' => ['currentEmailChecker'],
				'message' => 'Your inputted email does not match with the current email.',
			]
		),
		'new_email_address' => array(
			'New Email' => array(
				'rule' => array('notBlank'),
			),
            'Email Unique' => [
				'rule' => ['checkEmailIfAlreadyExists'],
				'message' => 'This email address is already in use.',
			],
			'Match Email' => [
				'rule' => ['matchEmails'],
				'message' => 'Your emails do not match.',
			]
		),
		'confirm_new_email_address' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please confirm your password.',
			)
		)
	);

	/**
	 * Before Validate
	 */
    public function beforeValidate($options = array()) {
		if (isset($this->data[$this->alias]['id'])) {
			$this->validate['email']['required'] = false;
			$this->validate['email']['allowEmpty'] = true;
		}

        return true;
    }

	/**
	 * Match Passwords
	 */
	public function matchPasswords($data) {
		if($data['password'] == $this->data['User']['confirm_password']) {
			return true;
		}

		return false;
	}

	/**
	 * Match Email Addresses
	 */
	public function matchEmails($data) {
		if($data['new_email_address'] == $this->data['User']['confirm_new_email_address']) {
			return true;
		}

		return false;
	}
	 
	/**
	 * Current Password Checker
	 */
	public function currentPasswordChecker() {
		$user = $this->findById(AuthComponent::user('id'));
		if($user['User']['password'] == Security::hash($this->data['User']['current_password'], null, true)) {
			return true;
		}

		return false;
	}

	/**
	 * Current Email Checker
	 */
	public function currentEmailChecker() {
		$user = $this->findById(AuthComponent::user('id'));
		if($user['User']['email'] == $this->data['User']['current_email_address']) {
			return true;
		}

		return false;
	}

	public function checkEmailIfAlreadyExists() {
		$user = $this->findByEmail($this->data['User']['new_email_address']);
		// debug(AuthComponent::user('email'));
		// debug($user);
		if(!$user) {
			return true;
		}

		return false;
	}

	/**
	 * beforeSave
	 */
	public function beforeSave($options = array()) {
		if(isset($this->data["User"]['password'])) {
			$this->data["User"]["password"] = Security::hash($this->data["User"]["password"], null, true);
		}
	}
}
