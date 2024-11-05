<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
 
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	//Users
	// Custom route for editing a user
	Router::connect('/edit/:id', 
	array('controller' => 'Users', 'action' => 'edit'),
	array('pass' => array('id'), 'id' => '[0-9]+')
	);
	Router::connect('/profile/:id', array('controller' => 'Users', 'action' => 'profile'));	
	Router::connect('/login', array('controller' => 'Users', 'action' => 'login'));
	Router::connect('/add', ['controller' => 'Users', 'action' => 'add']);
	Router::connect('/thankYou', ['controller' => 'Users', 'action' => 'thankYou']);
	Router::connect('/uploadImage', ['controller' => 'Users', 'action' => 'uploadImage']);
	
	//Messages
	Router::connect('/', ['controller' => 'Messages', 'action' => 'index']);
	Router::connect('/messageDetail/:chatId', 
	['controller' => 'Messages', 'action' => 'messageDetail'],
	['pass' => ['chatId'], 'chatId' => '\d+']);
	Router::connect('/replyMessage',['controller' => 'Messages', 'action' => 'replyMessage']);
	Router::connect('/add',['controller' => 'Messages', 'action' => 'add']);
	Router::connect('/getRecipient',['controller' => 'Messages', 'action' => 'getRecipient']);
	Router::connect('/deleteMessage',['controller' => 'Messages', 'action' => 'deleteMessage']);
	Router::connect('/deleteChat',['controller' => 'Messages', 'action' => 'deleteChat']);
/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
