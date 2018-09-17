<?php

/**
 * App Initial
 *
 */

namespace EatWhat;

use EatWhat\EatWhatRequest;
use EatWhat\EatWhatStatic;

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

		if( $this->config["developement"] ) {
			error_reporting(E_ALL);
			ini_set('display_errors','On');
    		ini_set('display_startup_errors','On');
		}

		ob_start("ob_gzhandler");

		session_start();

		$this->register();

        // create request
        $this->request = new EatWhatRequest();
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
		if($file && EatWhatStatic::checkFile($file)) require_once $file;
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
}