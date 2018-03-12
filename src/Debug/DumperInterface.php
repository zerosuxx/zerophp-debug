<?php

namespace Zero\Debug;

/**
 * DumperInterface
 *
 * @author Mohos Tamás <tomi@mohos.name>
 * @package Zero\Debug
 * @version 1.00
 */
interface DumperInterface {
    /**
     * @param array $vars
     */
    public function dump(array $vars);
    
    /**
     * @param array $vars
     * @param int $traceStartIndex
     */
    public function dumpTrace(array $vars, $traceStartIndex = 0);
}
