<?php

/**
 * App Initial
 *
 */

namespace EatWhat;

use EatWhat\EatWhatRequest;
use EatWhat\EatWhatStatic;
use EatWhat\MiddlewareGenerator;
use EatWhat\Exceptions\EatWhatException;

class AppInit
{

	/**
	 * App initalal config
	 *
	 */
	public $config;

    /**
     * request
     *
     */
    public $request;

	/**
	 * Initial app
	 *
	 */
	public function _Init($config)
	{
		$this->config = $config;

		ob_start("ob_gzhandler");

		session_start();

		$this->register();

		$this->initInput();

		// create request
		$this->request = new EatWhatRequest();
		$this->request->addMiddleWare(MiddlewareGenerator::generate("verifySign"));

		// invoke
		$this->request();
	}


	/**
	 * autoload
	 *
	 */
	public function register()
	{
		spl_autoload_register([$this, "autoLoadRegister"], false, true);
	}


	/**
	 * autoload method
	 *
	 */
	public function autoLoadRegister($class)
	{
		$file = $this->findFile($class);
		if($file && EatWhatStatic::checkFile($file)) 
			require_once $file;
		else 
			throw new EatWhatException("class file is not exists.");
	}

	/**
	 * autoload class method
	 *
	 */
	public function findFile($class) 
    {
        $subPath = $class;
        $suffix  = '';

        if( isset($this->config['classmap_static'][$class]) ) {
            $file = $this->config['classmap_static'][$class];
            return $file;
        }

        while( false !== $lastPos = strrpos($subPath, '\\') ) {
            $suffix = substr($subPath, $lastPos).$suffix;
            $suffix = str_replace('\\', DS, $suffix);
            $subPath = substr($subPath, 0, $lastPos);
            if ( isset($this->config['classmap_namespace'][$subPath]) ) {
                $file = $this->config['classmap_namespace'][$subPath].$suffix.$this->config['class_file_ext'];
                return $file;
            }
        }
	}
	
	/**
	 * init input
	 * 
	 */
	public function initInput()
	{
		EatWhatStatic::checkPostMethod() && ($_GET = array_merge($_GET, $_POST));
		$_GET["paramsSign"] = EatWhatStatic::getParamsSign();
	}
}