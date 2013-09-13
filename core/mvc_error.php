<?php

class MvcError {

	public static function fatal($message) {
		self::write('fatal', $message);
		die();
	}

	public static function warning($message) {
		self::write('warning', $message);
	}

	public static function notice($message) {
		self::write('notice', $message);
	}

	public static function debug_tracer(){
		$trace = debug_backtrace(); 

		$caller = array_shift($trace); 
		$function_name = $caller['function']; 
		error_log(sprintf('%s: Called from %s:%s', $function_name, $caller['file'], $caller['line'])); 
		foreach ($trace as $entry_id => $entry) { 
				$entry['file'] = $entry['file'] ? : '-'; 
				$entry['line'] = $entry['line'] ? : '-'; 
				if (empty($entry['class'])) { 
						error_log(sprintf('%s %3s. %s() %s:%s', $function_name, $entry_id + 1, $entry['function'], $entry['file'], $entry['line'])); 
				} else { 
						error_log(sprintf('%s %3s. %s->%s() %s:%s', $function_name, $entry_id + 1, $entry['class'], $entry['function'], $entry['file'], $entry['line'])); 
				} 
		} 

	}
	
	private static function write($type_key, $message) {
	
		$type_name = self::get_type($type_key);
		
		$context = self::get_context();
		$line = $context['line'];
		$file = $context['file'];
		
		$execution_context = MvcConfiguration::get('ExecutionContext');
		
		if ($execution_context == 'shell') {
		
			echo '-- '.$type_name.': '.$message."\n".'   (Thrown on line '.$line.' of '.$file.")\n";
		
		} else {
			error_log("[MVC] ".$type_name.": ".$message." Thrown on line ".$line." of ".$file."\n");
		}
	
	}
	
	private static function get_type($type_key) {
	
		$types = array(
			'fatal' => 'Fatal Error',
			'warning' => 'Warning',
			'notice' => 'Notice'
		);
		
		return $types[$type_key];
	
	}
	
	private static function get_context() {
	
		$backtrace = debug_backtrace();
		
		$context = empty($backtrace[3]['line']) ? $backtrace[2] : $backtrace[3];
		
		return $context;
	
	}

}

?>