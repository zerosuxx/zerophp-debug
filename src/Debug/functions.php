<?php

if( !function_exists('p') ) {
    function p() {
        Zero\Debug\Debugger::registerDumpHandler();
        Zero\Debug\Debugger::dumpArgs(func_get_args(), 1);
    }
}

if( !function_exists('_die') ) {
    function _die() {
        Zero\Debug\Debugger::registerDumpHandler();
        Zero\Debug\Debugger::dumpArgs(func_get_args(), 1);
        die();
    }
}