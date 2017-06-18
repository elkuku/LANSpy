<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 17/06/17
 * Time: 12:16
 */

use Pingtest\Application;

require_once '../vendor/autoload.php';

echo (new Application(realpath(__DIR__.'/..')))
	->execute();
