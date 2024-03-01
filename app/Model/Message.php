<?php
App::uses('AppModel', 'Model');
/**
 * Message Model
 *
 * @property Thread $Thread
 * @property User $User
 */
class Message extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'thread_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
		'content' => array(
			'notBlank' => array(
				'rule' => array('notBlank')
			),
		),
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Thread' => array(
			'className' => 'Thread',
			'foreignKey' => 'thread_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
