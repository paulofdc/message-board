<?php
App::uses('AppController', 'Controller');
/**
 * Threads Controller
 *
 * @property Thread $Thread
 * @property Message $Message
 * @property PaginatorComponent $Paginator
 */
class ThreadsController extends AppController {

	public const DEFAULT_PAGE_SIZE = 5;

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator');

	public $uses = array('Thread', 'Message');
	
	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$ownerId = $this->Auth->user('id');
		$threads = $this->Thread->find('all', [
			'order' => 'Thread.created DESC',
			'contain' => [
				'Message' => [
					'order' => 'Message.created DESC',
					'limit' => 1
				],
				'Owner',
				'Receiver',
			],
			'conditions' => [
				'OR' => [
					'Thread.owner_id' => $ownerId,
					'Thread.receiver_id' => $ownerId
				]
			],
			'limit' => self::DEFAULT_PAGE_SIZE
		]);

		$currentUserId = $this->Auth->user('id');
		$threadCount = $this->Message->query("SELECT COUNT(*) as thread_count FROM threads WHERE owner_id = ($currentUserId) OR receiver_id = ($currentUserId)")[0][0]['thread_count'];
		$this->set([
			'maxLimit' => self::DEFAULT_PAGE_SIZE,
			'threadCount' => $threadCount,
			'threads' => $threads
		]);
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		$thread = $this->Thread->findById($id);
		if(!$thread) {
			$this->redirect('/');
		}

		$toCheck = [$thread["Thread"]['owner_id'], $thread["Thread"]['receiver_id']];
		if(!in_array($this->Auth->user('id'), $toCheck)) {
			$this->redirect('/');
		}

		$messageCount = $this->Message->query("SELECT COUNT(*) as message_count FROM messages WHERE thread_id = ($id)")[0][0]['message_count'];
		$messages = $this->Message->find('all', [
			'order' => 'Message.created DESC',
			'conditions' => [
				'thread_id' => $id
			],
			'limit' => self::DEFAULT_PAGE_SIZE
		]);
		$participant = $thread["Owner"]['id'] == $this->Auth->user('id') ? 
						$thread["Receiver"]['name'] : $thread["Owner"]['name'];
		$this->set([
			'maxLimit' => self::DEFAULT_PAGE_SIZE,
			'messageCount' => $messageCount,
			'participant' => $participant,
			'threadId' => $id,
			'messages' => $messages
		]);
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			$userId = $this->Auth->user('id');
			$this->request->data['Thread']['owner_id'] = $userId;
			$this->request->data['Thread']['receiver_id'] = explode('~USER:', $this->request->data['Thread']['receivers'])[0];
			$message = $this->request->data['Thread']['message'];
			unset($this->request->data['Thread']['receivers']);
			unset($this->request->data['Thread']['message']);

			$this->Thread->create();
			if ($this->Thread->save($this->request->data)) {
				$threadId = $this->Thread->id;
				$this->Message->create();
				if ($this->Message->save([
						'Message' => [
							'thread_id' => $threadId,
							'user_id' => $userId,
							'content' => $message
						]
					])
				) {
					$this->Flash->success(__('The message has been created.'));
					return $this->redirect(['action' => 'index']);
				} else {
					$this->Thread->delete($threadId);
					$this->Flash->error(__('Failed to create message.'));
				}
			} else {
				$this->Flash->error(__('The message could not be created. Please, try again.'));
			}
		}

		$receivers = $this->Thread->Receiver->find('all', [
			'conditions' => [
				'Receiver.id !=' => $this->Auth->user('id')
			],
			'fields' => ['CONCAT(id, IF(photo IS NOT NULL, CONCAT("~USER:", photo), "")) AS id_photo', 'name'],
			'recursive' => -1
		]);

		$restructuredReceivers = [];
		foreach ($receivers as $receiver) {
			$restructuredReceivers[$receiver[0]['id_photo']] = $receiver['Receiver']['name'];
		}

		$this->set(['receivers' => $restructuredReceivers]);
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		if (!$this->Thread->exists($id)) {
			throw new NotFoundException(__('Invalid thread'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Thread->save($this->request->data)) {
				$this->Flash->success(__('The thread has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The thread could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Thread.' . $this->Thread->primaryKey => $id));
			$this->request->data = $this->Thread->find('first', $options);
		}
		$owners = $this->Thread->Owner->find('list');
		$receivers = $this->Thread->Receiver->find('list');
		$this->set(compact('owners', 'receivers'));
	}

	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		if (!$this->Thread->exists($id)) {
			throw new NotFoundException(__('Invalid thread'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Thread->delete($id)) {
			$this->Flash->success(__('The thread has been deleted.'));
		} else {
			$this->Flash->error(__('The thread could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	/**
	 * Load More for both Threads and Messages
	 */
	public function loadMore() {
		$this->autoRender = false;
		$requestData = $this->request->data;
		$type = ucfirst($requestData['searchType']) ?? '';

		$allowedTypes = ['Thread', 'Message'];
		if(!in_array($type, $allowedTypes)) {
			echo json_encode([
				'message' => __('Not allowed.')
			]);
			return;
		}

		$hasLastData = false;
		$query = [
			'order' => $type .'.created DESC',
			'conditions' => [
				$type . '.id <' => $requestData['currentLatestOldestId']
			]
		];

		$currentUserId = $this->Auth->user('id');
		if($type == 'Message') {
			$query['conditions']['thread_id'] = $requestData['thread_id'];
		} else if ($type == 'Thread') {
			$query['contain'][] = 'Owner';
			$query['contain'][] = 'Receiver';
			$query['contain']['Message']['order'] = 'Message.created DESC';
			$query['contain']['Message']['limit'] = 1;
			$query['conditions']['OR'] = [
				'Thread.owner_id' => $currentUserId,
				'Thread.receiver_id' => $currentUserId
			];
		}

		$lastRecordQuery = $query;
		$lastRecordQuery['order'] = $type . '.created ASC';
		$lastRecord = $this->$type->find('first', $lastRecordQuery);

		$query['limit'] = self::DEFAULT_PAGE_SIZE;
		$data = $this->$type->find('all', $query);
		foreach($data as $key => $row) {
			$hasLastData = ($lastRecord[$type]['id'] == $row[$type]['id']);
			$data[$key][$type]['created'] = $this->dateToString($row[$type]['created'], true);
		}

		echo json_encode([
			'query' => $query,
			'hasLastData' => $hasLastData,
			'data' => $data
		]);
	}

	public function search() {
		$this->autoRender = false;
		$requestData = $this->request->data;
		$searchKeyword = $requestData['searchValue'];

		$query = [
			'order' => 'Message.created DESC',
			'conditions' => [
				'thread_id' => $requestData['thread_id'],
				'Message.content LIKE' => '%' . $searchKeyword . '%'
			]
		];

		$messages = $this->Message->find('all', $query);
		foreach($messages as $key => $message) {
			$messages[$key]['Message']['created'] = $this->dateToString($message['Message']['created'], true);
		}

		echo json_encode([
			'data' => $messages
		]);
	}
}
