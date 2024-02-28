<?php
App::uses('AppController', 'Controller');
/**
 * Home Controller
 *
 * @property Home $Home
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 * @property FlashComponent $Flash
 */
class HomeController extends AppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Session', 'Flash');

    public function beforeFilter()
	{
		$this->Auth->allow(['index']);
	}

	public function index() {

	}

	public function greetings() {
		
	}
}
