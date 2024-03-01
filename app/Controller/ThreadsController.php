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
		$this->Thread->recursive = 0;
		$this->set('threads', $this->Paginator->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		if (!$this->Thread->exists($id)) {
			throw new NotFoundException(__('Invalid thread'));
		}
		$options = array('conditions' => array('Thread.' . $this->Thread->primaryKey => $id));
		$this->set('thread', $this->Thread->find('first', $options));
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
			$this->request->data['Thread']['receiver_id'] = $this->request->data['Thread']['receivers'];
			$message = $this->request->data['Thread']['message'];
			unset($this->request->data['Thread']['receivers']);
			unset($this->request->data['Thread']['message']);

			$this->Thread->create();
			if ($this->Thread->save($this->request->data)) {
				$threadId = $this->Thread->id;
				debug($threadId);
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

		$receivers = $this->Thread->Receiver->find('list');
		$this->set(compact('receivers'));
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
}
