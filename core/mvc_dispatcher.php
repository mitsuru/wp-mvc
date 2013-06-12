<?php

class MvcDispatcher {

	public static $controller;

	function dispatch($options=array()) {
		
		$controller_name = $options['controller'];
		$action = $options['action'];
		$params = $options;
		
		$controller_class = MvcInflector::camelize($controller_name).'Controller';
		
		MvcDispatcher::$controller = new $controller_class();
		
		MvcDispatcher::$controller->name = $controller_name;
		MvcDispatcher::$controller->action = $action;
		MvcDispatcher::$controller->init();
		
		if (!method_exists(MvcDispatcher::$controller, $action)) {
			MvcError::fatal('A method for the action "'.$action.'" doesn\'t exist in "'.$controller_class.'"');
		}
		
		$request_params = $_REQUEST;
		$request_params = self::escape_params($request_params);
		
		$params = array_merge($request_params, $params);
		
		if (is_admin()) {
			unset($params['page']);
		}
		
		MvcDispatcher::$controller->params = $params;
		MvcDispatcher::$controller->set('this', MvcDispatcher::$controller);
		if (!empty(MvcDispatcher::$controller->before)) {
			foreach (MvcDispatcher::$controller->before as $method) {
				MvcDispatcher::$controller->{$method}();
			}
		}
		MvcDispatcher::$controller->{$action}();
		if (!empty(MvcDispatcher::$controller->after)) {
			foreach (MvcDispatcher::$controller->after as $method) {
				MvcDispatcher::$controller->{$method}();
			}
		}
		MvcDispatcher::$controller->after_action($action);
		
		if (!MvcDispatcher::$controller->view_rendered) {
			MvcDispatcher::$controller->render_view($controller->views_path.$action, $options);
		}
	
	}
	
	private function escape_params($params) {
		if (is_array($params)) {
			foreach ($params as $key => $value) {
				if (is_string($value)) {
					$params[$key] = stripslashes($value);
				} else if (is_array($value)) {
					$params[$key] = self::escape_params($value);
				}
			}
		}
		return $params;
	}

	public function __call($method, $args) {
		if (isset($this->$method) === true) {
			$function = $this->$method;
			$function();
		}
	}

}

?>