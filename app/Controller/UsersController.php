<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class UsersController extends AppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator');

	public function beforeFilter() {
		$this->Auth->allow(['add']);
	}

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->Paginator->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$this->set('user', $this->User->find('first', $options));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->Auth->user()) {
			return $this->redirect('/');
		}

		if ($this->request->is('post')) {
			$filename = $this->uploadImage();
			switch($filename) {
				case false:
					$this->Flash->error(__('An error occured during uploading photo. Please try again.'));
					break;
				case 'E-1000':
					$this->Flash->error(__('Only gif, jpg, jpeg, and png are allowed.'));
					break;
				case 'E-EMPTY':
					$filename = NULL;
				default:
					$this->saveUser($filename);
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
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if($this->Auth->user('id') != $id) {
			return $this->redirect(['controller' => 'home', 'action' => 'index']);
		}

		if ($this->request->is(array('post', 'put'))) {
			$filename = $this->uploadImage();
			debug($filename);
			switch($filename) {
				case false:
					$this->Flash->error(__('An error occured during uploading photo. Please try again.'));
					break;
				case 'E-1000':
					$this->Flash->error(__('Only gif, jpg, jpeg, and png are allowed.'));
					break;
				case 'E-EMPTY':
					$filename = $this->Auth->user('photo');
				default:
					$this->saveUser($filename, true);
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
	}

	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->User->delete($id)) {
			$this->Flash->success(__('The user has been deleted.'));
		} else {
			$this->Flash->error(__('The user could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	/**
	 * Login
	 */
	public function login() {
		if ($this->Auth->user()) {
			return $this->redirect('/');
		}

		if($this->request->is('post')) {
			if($this->Auth->login()) {
				$this->updateUserLoginTime($this->Auth->user('id'));
				return $this->redirect($this->Auth->redirectUrl());
			} else {
				$this->Flash->error(__('Invalid email or password'));
			}
		}
	}

	/**
	 * View Profile
	 */
	public function profile($id) {
		$user = $this->User->findById($id);

		if (!$user) {
			return $this->redirect('/');
		}

		$userData = $user['User'];
		$userData['age'] = $this->birthdateToAgeConverter($userData['birthdate']);
		$userData['birthdate'] = $this->dateToString($userData['birthdate']);
		$userData['date_joined'] = $this->dateToString($userData['created'], true);
		$userData['last_login'] = $this->dateToString($userData['last_login'], true);
		$this->set('user', $userData);
	}

	/**
	 * Change Password
	 */
	public function changePassword($id) {
		if($id != $this->Auth->user('id')) {
			$this->redirect('/');
		}

		if($this->request->is('post')) {
			$this->User->set($this->request->data);
			if ($this->User->validates()) {
				$user = $this->User->findById($id);
				if (!$user) {
					throw new NotFoundException(__('User not found'));
				}

				$this->User->id = $id;
				$this->User->saveField('password', $this->request->data['User']['password']);
				return $this->redirect(['action' => 'profile', $id]);
			}
		}
	}

	/**
	 * Change Email Address
	 */
	public function changeEmailAddress($id) {
		if($id != $this->Auth->user('id')) {
			$this->redirect('/');
		}

		if($this->request->is('post')) {
			$this->User->set($this->request->data);
			if ($this->User->validates()) {
				$user = $this->User->findById($id);
				if (!$user) {
					throw new NotFoundException(__('User not found'));
				}

				$this->User->id = $id;
				$this->User->saveField('email', $this->request->data['User']['new_email_address']);
				return $this->redirect(['action' => 'profile', $id]);
			}
		}
	}

	/**
	 * Logout
	 */
	public function logout() {
		$this->Auth->logout();
		return $this->redirect('/users/login');
	}

	/**
	 * Update User 'login_time'
	 */
	public function updateUserLoginTime($userId) {
		$user = $this->User->findById($userId);

		if (!$user) {
			throw new NotFoundException(__('User not found'));
		}

		$this->User->id = $userId;
		$this->User->saveField('last_login', date("Y-m-d H:i:s"));
	}

	/**
	 * Upload Image
	 */
	public function uploadImage() {
		try {
			if (!empty($this->request->data['User']['photo']['name'])) {
				$photo = $this->request->data['User']['photo'];
		
				$allowedExtensions = array('jpg', 'jpeg', 'gif', 'png');
				$fileExtension = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));
				
				if (!in_array($fileExtension, $allowedExtensions)) {
					return "E-1000";
				}

				$uploadDir = WWW_ROOT . 'uploads';
		
				if (!is_dir($uploadDir)) {
					mkdir($uploadDir);
				}
		
				$filename = uniqid() . '_' . $photo['name'];
		
				if (move_uploaded_file($photo['tmp_name'], $uploadDir . DS . $filename)) {
					return "/uploads/" . $filename;
				} else {
					return false;
				}
			} else {
				return "E-EMPTY";
			}
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Save User
	 */
	public function saveUser($filename, $isEdit = false) {
		$this->User->create();
		$this->request->data['User']['photo'] = $filename;

		if ($this->User->save($this->request->data)) {
			if ($this->Auth->login()) {
				$this->updateUserLoginTime($this->Auth->user('id'));

				if($isEdit) {
					$this->Session->write('Auth.User', $this->request->data['User']);
					$this->Flash->success(__('The user has been saved.'));
					return $this->redirect(['action' => 'profile', $this->Auth->user('id')]);
				}

				return $this->redirect(['controller' => 'home', 'action' => 'greetings']);
			}
		} else {
			$this->Flash->error(__('The user could not be saved. Please, try again.'));
		}
	}
}
