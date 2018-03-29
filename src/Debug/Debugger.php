<?php

namespace Zero\Debug;

/**
 * Debugger
 *
 * @author Mohos Tamás <tomi@mohos.name>
 * @package Zero\Debug
 * @version 2.01
 */
class Debugger {
    /** @var DumperInterface */
    protected static $dumper = null;
    
    /** 
     * @param DumperInterface $dumper
     */
    public static function setDumper(DumperInterface $dumper) {
		static::$dumper = $dumper;
	}
    
    /**
     * @return DumperInterface
     */
    public static function getDumper() {
        if(null === static::$dumper) {
            static::restoreDefaultDumper();
        }
		return static::$dumper;
	}
    
    /**
     * @return void
     */
    public static function restoreDefaultDumper() {
        $printer = function_exists('dump') ? 'dump' : [get_called_class(), 'varDump'];
        static::$dumper = new Dumper($printer);
    }
    
    /**
     * @param mixed $vars [optional]
     * @return void
     */
    public static function dump($vars = null) {
        if(!is_array($vars)) {
            $vars = [$vars];
        }
        static::getDumper()->dump((array)$vars);
    }
    
    /**
     * @param array $vars [optional]
     * @param int $traceStartIndex [optional] <p>default: 0</p>
     * @return void
     */
    public static function dumpTrace($vars = null, $traceStartIndex = 0) {
        if(!is_array($vars)) {
            $vars = [$vars];
        }
        static::getDumper()->dumpTrace((array)$vars, $traceStartIndex+1);
    }
    
    /**
     * Dump Die
     * 
     * @param array $vars [optional]
     * @return void
     */
    public static function dd($vars = null) {
        static::dump($vars);
        die();
    }
    
    /**
     * Dump Trace Die
     * 
     * @param array $vars [optional]
     * @param int $traceStartIndex [optional] <p>default: 0</p
     * @return void
     */
    public static function dtd($vars = null, $traceStartIndex = 0) {
        static::dumpTrace($vars, $traceStartIndex+1);
        die();
    }
}