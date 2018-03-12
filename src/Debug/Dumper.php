<?php

namespace Zero\Debug;

/**
 * Dumper
 *
 * @author Mohos Tamás <tomi@mohos.name>
 * @package Zero\Debug
 * @version 1.00
 */
class Dumper implements DumperInterface {
    private $printer;
    
    /**
     * @param callable $printer [optional]
     */
    public function __construct(callable $printer = null) {
        if(null === $printer) {
            $printer = 'var_dump';
        }
        $this->printer = $printer;
    }

    /**
     * @param array $vars
     */
    public function dump(array $vars) {
        $printer = $this->printer;
        foreach((array)$vars as $arg) {
            $printer($arg);
        }
    }
    
    /**
     * 
     * @param array $vars
     * @param int $traceStartIndex [optional] <p>default: 0</p>
     */
    public function dumpTrace(array $vars, $traceStartIndex = 0) {
        $printer = $this->printer;
        $trace = $this->getLastTrace($traceStartIndex+1);
        $formattedTrace = $this->traceToString($trace);
        $printer($formattedTrace);
        foreach((array)$vars as $arg) {
            $printer($arg);
        }
    }
    
    /**
     * @param int $startIndex [optional] <p>default: 0</p>
     * @return array
     */
    public function getLastTrace($startIndex = 0) {
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
        if($startIndex && $lastIndex >= ($index+$startIndex)) {
           $index += $startIndex;
        }
        return $traces[$index];
    }
    
    /**
     * @param array $trace
     * @return string
     */
    public function traceToString(array $trace) {
        if(!isset($trace['file'])) {
            return '[internal function]';
        }
        $argsString = '';
        if(isset($trace['args'])) {
            $formattedArgs = array_map([$this, 'getType'], $trace['args']);
            $argsString = implode(', ', $formattedArgs);
        }
        $output = $trace['file'] . ':' . $trace['line'].' #';
        if(isset($trace['class'])) {
            $output .= ' ' . $trace['class'] . $trace['type'];
        }
        $output .= ' ' . $trace['function'] . '('.$argsString.')';
        return $output;
    }
    
    /**
     * @param mixed $var
     * @return string
     */
    public function getType($var) {
        return is_object($var) ? get_class($var) : gettype($var);
    }
    
    /**
     * @return string
     */
    public function getTraceString() {
        return (new Exception)->getTraceAsString();
    }

}
