<?php

/**
*	
*	PHP variable rendering engine
*	
**/

class MvcVariableRender extends MvcRender {

	function render($filepath, $view_vars = array())
  {
		extract($view_vars);
    if(file_exists($filepath))
    {
      require $filepath;
    }
    else
    {
      MvcError::notice("Attempted to render non-existant file: " . $filepath);
    }
	}

}