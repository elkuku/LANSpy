<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 17/06/17
 * Time: 12:16
 */

use Pingtest\Application;
use Dbug\BittrDbug;

require_once '../vendor/autoload.php';

new BittrDbug(BittrDbug::PRETTIFY, 'yola', 20);

echo (new Application(realpath(__DIR__.'/..')))
    ->execute();
