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
	 * Before Save
	 */
    public function beforeSave($options = array()) {
        parent::beforeSave($options);
        if (!empty($this->data['Message']['thread_id'])) {
            $threadId = $this->data['Message']['thread_id'];
            $this->Thread->id = $threadId;
            $this->Thread->saveField('modified', $this->data['Message']['created']);
        }
        return true;
    }

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
			'Message Content' => array(
				'rule' => array('notBlank'),
				'message' => 'Message is required'
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
