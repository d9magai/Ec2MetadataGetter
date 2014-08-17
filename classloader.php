<?php
/*
 * PSR-0 compliant simple classloader
 * see:http://qiita.com/Hiraku/items/72251c709503e554c280
 */
spl_autoload_register(function ($c)
{
    include_once strtr($c, '\\_', '//') . '.php';
});

