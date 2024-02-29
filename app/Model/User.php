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
			),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'This email address is already in use'
            ),
		),
		'password' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
			'Match passwords' => [
				'rule' => ['matchPasswords'],
				'message' => 'Your passwords do not match',
			]
		),
		'confirm_password' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please confirm your password',
			)
		),
		'gender' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'photo' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => false,
				'allowEmpty' => true,
			),
		),
		'birthdate' => array(
			'date' => array(
				'rule' => array('date'),
				//'message' => 'Your custom message here',
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
				'allowEmpty' => true,
				'required' => false,
			),
		),
	);

	/**
	 * matchPasswords
	 */
	public function matchPasswords($data) {
		if($data['password'] == $this->data['User']['confirm_password']) {
			return true;
		}

		$this->invalidate('confirm_password', 'Your passwords do not match');

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
