<?php

if( !function_exists('p') ) {
    function p() {
        Zero\Debug\Debugger::dumpTrace(func_get_args(), 1);
    }
}

if( !function_exists('_die') ) {
    function _die() {
        Zero\Debug\Debugger::dtd(func_get_args(), 1);
        die();
    }
}