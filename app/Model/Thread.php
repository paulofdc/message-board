<?php
App::uses('AppModel', 'Model');
/**
 * Thread Model
 *
 * @property User $Owner
 * @property User $Receiver
 * @property Message $Message
 */
class Thread extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'owner_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
		'receiver_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
	);
	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Owner' => array(
			'className' => 'User',
			'foreignKey' => 'owner_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Receiver' => array(
			'className' => 'User',
			'foreignKey' => 'receiver_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'Message' => array(
			'className' => 'Message',
			'foreignKey' => 'thread_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
