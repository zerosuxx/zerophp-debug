<?php

namespace Zero\Debug;

/**
 * Debugger
 *
 * @author Mohos Tamás <tomi@mohos.name>
 * @package Zero\Debug
 * @version 1.07
 */
class Debugger {
    /** @var double */
    const VERSION = 1.07;
    /** @var callable */
    protected static $_handler = [__CLASS__, 'varDump'];
    /** @var callable */
    protected static $_debugInfo = null;
    
    public static function dump() {
        static::dumpArgs(func_get_args(), 1);
    }
    
    /**
     * @param array $args [optional]
     * @param int $traceLevel [optional] <p>default: 0</p>
     * @param boolean|callable $debugInfo [optional] <p>default: false</p>
     * @todo debug info
     */
    public static function dumpArgs(array $args = [], $traceLevel = 0, $debugInfo = false) {
        $header = '';
        $handler = static::$_handler;
		if($handler) {
			if($traceLevel !== false) {
				$trace = static::getLastTrace($traceLevel+1);
				$header = static::formatTrace($trace);
			}
			if($debugInfo) {
				$debugInfoCallable = static::debugInfo();
                if($debugInfoCallable) {
				    $header .= ' | '.$debugInfoCallable();
                }
                //$header .= ' | '.var_export(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20), true);
			}
			if($header) {
				$handler($header);
			}
			if($args) {
				foreach((array)$args as $arg) {
					$handler($arg);
				}
			}
		}
    }
    
    public static function setDefaultHandler() {
		static::$_handler = [get_called_class(), 'varDump'];
	}
    
    public static function setHandler(callable $handler) {
		static::handler($handler);
	}
    
    public static function getHandler() {
		return static::handler();
	}
	
	public static function removeHandler() {
		static::$_handler = null;
	}
	
	public static function varDump($var) {
        if( !static::isCli() ) {
	        echo '<pre>';
        } else {
            echo "\r\n";
        }
	    var_dump($var);
	    if( !static::isCli() ) {
	        echo '</pre>';
        }
	}
    
    public static function handler(callable $handler = null) {
        if($handler !== null) {
            static::$_handler = $handler;
        }
        return static::$_handler;
    }
    
    public static function debugInfo(callable $callable = null) {
        if($callable !== null) {
            static::$_debugInfo = $callable;
        }
        return static::$_debugInfo;
    }
    
    public static function getLastTrace($level = 0) {
        $traces = debug_backtrace();
        $count = count($traces);
        $index = 0;
        $lastIndex = $count-1;
        if($count > 1) {
            foreach(array_slice($traces, 1) as $k => $trace) {
                if(isset($trace['function']) && $trace['function'] === '{closure}') {
                    $index++;
                } else if($index > 0){
                    $index++;
                    break;
                } else if($index === 0 && $k > 0) {
                    break;
                }
            }
        }
        if($level && $lastIndex >= ($index+$level)) {
           $index += $level;
        }
        return $traces[$index];
    }
    
    /**
     * @param array $trace
     * @return string
     */
    public static function formatTrace(array $trace) {
        $argsString = isset($trace['args']) ? implode(', ', array_map([get_called_class(), 'getType'], $trace['args'])) : '';
        return isset($trace['file']) ? $trace['file'].':'.$trace['line'].' # '.(isset($trace['class']) ? $trace['class'].$trace['type'] : '').$trace['function'].'('.$argsString.')' : '[internal function]';
    }
    
    /**
     * @param mixed $var
     * @return string
     */
    public static function getType($var) {
        return is_object($var) ? get_class($var) : gettype($var);
    }
    
    /**
     * @return string
     */
    public static function getTraceString() {
        return (new Exception)->getTraceAsString();
    }
    
    /**
     * @return void
     */
    public static function dd() {
        static::dumpArgs(func_get_args(), 1);
        die();
    }
    
    /**
     * @return boolean
     */
    public static function isCli() {
        return php_sapi_name() === 'cli';
    }
    
    public static function registerDumpHandler() {
        if( static::getHandler() !== 'dump' && function_exists('dump') ) {
            static::setHandler('dump');
        }
    }
}