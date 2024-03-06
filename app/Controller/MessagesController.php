<?php
App::uses('AppController', 'Controller');
/**
 * Messages Controller
 *
 * @property Thread $Thread
 * @property Message $Message
 * @property PaginatorComponent $Paginator
 */
class MessagesController extends AppController {

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
		$this->Message->recursive = 0;
		$this->set('messages', $this->Paginator->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		if (!$this->Message->exists($id)) {
			throw new NotFoundException(__('Invalid message'));
		}
		$options = array('conditions' => array('Message.' . $this->Message->primaryKey => $id));
		$this->set('message', $this->Message->find('first', $options));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
        if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$requestData = $this->request->data;

			$result = false;
			$thread = $this->Thread->findById($requestData['thread_id']);
			if(!$thread) {
				echo json_encode([
					'message' => __('Thread not found.')
				]);
				return;
			}

			$toCheck = [$thread["Thread"]['owner_id'], $thread["Thread"]['receiver_id']];
			if(!in_array($this->Auth->user('id'), $toCheck)) {
				echo json_encode([
					'message' => __('Not allowed.')
				]);
				return;
			}

			$this->Message->create();
			if ($this->Message->save([
				'Message' => [
					'thread_id' => $requestData['thread_id'],
					'user_id' => $this->Auth->user('id'),
					'content' => $requestData['content']
				]
			])) {
				$result = true;
			}

			$insertedId = $this->Message->getInsertID();
            echo json_encode([
				'isSuccess' => $result,
				'dataId' => $insertedId,
				'created' => ($result) ? $this->dateToString($this->Message->field('created', ['id' => $insertedId]), true) : '',
				'messageOwner' => $this->Auth->user('id')
			]);

            return;
        }

		if ($this->request->is('post')) {
			$this->Message->create();
			$threadId = $this->request->data['Message']['threadId'];
			unset($this->request->data['Message']['threadId']);
			$this->request->data['Message']['thread_id'] = $threadId;
			$this->request->data['Message']['user_id'] = $this->Auth->user('id');
			if ($this->Message->save($this->request->data)) {
				$this->Flash->success(__('The message has been sent.'));
				return $this->redirect(['controller' => 'threads','action' => 'view', $threadId]);
			} else {
				$this->Flash->error(__('The message could not be saved. Please, try again.'));
				return $this->redirect(['controller' => 'threads','action' => 'view', $threadId]);
			}
		}
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		if (!$this->Message->exists($id)) {
			throw new NotFoundException(__('Invalid message'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Message->save($this->request->data)) {
				$this->Flash->success(__('The message has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The message could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Message.' . $this->Message->primaryKey => $id));
			$this->request->data = $this->Message->find('first', $options);
		}
		$threads = $this->Message->Thread->find('list');
		$users = $this->Message->User->find('list');
		$this->set(compact('threads', 'users'));
	}

	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {

        if ($this->request->is('ajax')) {
			$this->autoRender = false;

			$result = false;
			$id = $this->request->data['id'];
			$message = $this->Message->findById($id);

			if (!$message) {
				echo json_encode([
					'message' => __('Message does not exist anymore. Please reload the page.')
				]);
				return;
			}

			if($message['Message']['user_id'] != $this->Auth->user('id')) {
				echo json_encode([
					'message' => __('Not allowed.')
				]);
				return;
			}

			$this->request->allowMethod('post', 'delete');
			if ($this->Message->delete($id)) {
				$result = true;
			}

            echo json_encode([
				'isSuccess' => $result
			]);
			return;
        }

		if (!$this->Message->exists($id)) {
			throw new NotFoundException(__('Invalid message'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Message->delete($id)) {
			$this->Flash->success(__('The message has been deleted.'));
		} else {
			$this->Flash->error(__('The message could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
