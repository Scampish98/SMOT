<?php
namespace tests;
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use server\db.php;

class GuestbookTest extends TestCase
{
    use TestCaseTrait;
    public function getConnection()
    {
        $pdo = new PDO('mysql:host=localhost;dbname=testdb', 'root', '');
        return $this->createDefaultDBConnection($pdo, 'testdb');
    }
    public function testClear()
    {

        $querydb = $this->clear()
        $expecteddb = $this->createXmlDataSet("expecteddb.xml")
        $this->assertTablesEqual($expectedTable, $queryTable);
    }
    public function testGetstopid(){
    	$id = 3;
    	$expectedTable = $this->getConnection()->createQueryTable('stop', 'SELECT id FROM stop');
        $this->assertDataSetsEqual($expectedTable, getPointByStopId ($id));
    }
    public function testgetRouteAsStops(){
    	$id = 2;
    	$expectedTable = $this->getConnection()->createQueryTable('stop', 'SELECT intRouteId FROM tblStopRoute WHERE intStopId =  $id');
        $this->assertDataSetsEqual($expectedTable, getRoutesByStopId ($id));
    }
    public function testgetStopByNum(){
	$numStop = 1;
	$idRoute = 2;
	$dir = 3;
    $expectedTable = $this->getConnection()->createQueryTable('stop', 'SELECT intStopId FROM tblStopRoute WHERE intStopNum =  $numStop and intRouteId = $idRoute and boolDirection = $dir');
    $this->assertDataSetsEqual($expectedTable, getRouteIds ($numStop, $idRoute, $dir));
    }
        public function testgetRouteIds(){
    	$id = 2;
    	$expectedTable = $this->getConnection()->createQueryTable('stop', 'SELECT intRouteId FROM tblStopRoute WHERE intStopId = $id');
        $this->assertDataSetsEqual($expectedTable, getRouteIds ($id));
    }
}