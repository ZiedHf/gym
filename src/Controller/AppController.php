<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Network\Session\DatabaseSession;
use Cake\I18n\I18n;
use Cake\Datasource\ConnectionManager;
/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
//	public $helpers = ['AkkaCKEditor.CKEditor'];
	 public function initialize()
    {
        parent::initialize();

				// Set the database
				if(isset($this->request->data['gym'])){
					$database = $this->request->data['gym'];
					$this->request->session()->write('database', $database);
				}else{
					$database = $this->request->session()->read('database');
				}


				if(isset($database)){
					ConnectionManager::alias($database, 'default');
				}elseif(!in_array($this->request->param('controller'), ['Users', 'MemberRegistration'])){
					return $this->redirect(["controller" => "users", "action" => "logout"]);
				}

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
				$this->loadComponent(
							'Auth',[
								'loginRedirect'=>['controller'=>'Dashboard','action'=>'index'],
             		'logoutRedirect'=>['controller'=>'Users',"action"=>"login"],
								'authenticate' => [
				            'Form' => [
				                'fields' => ['username' => 'email', 'password' => 'password']
				            ]
				        ],
               	'authorize' => array('Controller')
                ]);
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */

	public function isAuthorized($users)
    {
        if(isset($users))
        {
            return true;
        }
        else{
            return false;
        }
    }


    public function beforeRender(Event $event)
    {
		if(file_exists(TMP.'installed.txt'))
		{
			$this->loadComponent("GYMFunction");
			$check_alert_on = $this->GYMFunction->getSettings("enable_alert");
			if($check_alert_on)
			{
				$this->GYMFunction->sendAlertEmail();
			}
		}
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }

 	public function beforeFilter(Event $event)
	{
		parent::beforeFilter($event);
		/* $session = $this->request->session();
		 if($session->read("User") != null)
		{  */
		if(file_exists(TMP.'installed.txt') && $this->request->controller != "Installer")
		{
			$this->loadComponent("GYMFunction");
			@$lang = $this->GYMFunction->getSettings("sys_language");
			if (empty($lang)) {
				return;
			}
            I18n::locale($lang);
		}
		/* } */
	}
}
