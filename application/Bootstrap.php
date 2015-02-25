<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
	public function _init(){
		
		$route = new Zend_Controller_Router_Route(':action',
				array('controller' => 'index'));
		
		$ctrl = Zend_Controller_Front::getInstance();
		$router = $ctrl->getRouter();
		
		$router->addRoute('roleCheckRoute', $route);
	}
}

