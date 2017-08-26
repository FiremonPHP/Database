<?php
/**
 * Created by PhpStorm.
 * User: Marcos
 * Date: 24/08/2017
 * Time: 17:48
 */

namespace Connection;

use FiremonPHP\Database\Connection\ConnectionManager;

class ConnectionManagerTest extends \PHPUnit\Framework\TestCase
{
    public function testSum()
    {
        $m = new ConnectionManager();
        $this->assertEquals(5, $m->sum(2, 3));
    }
}
