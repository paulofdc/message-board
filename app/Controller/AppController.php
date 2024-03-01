<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    public $components = [
        'Session',
        'Flash',
        'Auth' => [
			'authenticate' => [
				'Form' => [
					'fields' => ['username' => 'email']
				]
            ],
            'authError' => "You can't access that page",
            'authorize' => ['Controller']
        ], 
    ];

    public function isAuthorized() {
        return true;
    }

	/**
	 * Birthdate Converter
	 */
	public function birthdateToAgeConverter($birthdate) {
		$birthdate = new DateTime($birthdate);
		$currentDate = new DateTime();

		$diff = $currentDate->diff($birthdate);
		return $diff->y;
	}

	/**
	 * Date to String
	 */
	public function dateToString($date, $isTimeIncluded = false) {
		$date = new DateTime($date);
		$pattern = ($isTimeIncluded) ? 'F j, Y ga' : 'F j, Y';
		return $date->format($pattern);
	}
}
