<?php
namespace tests;
use PHPUnit\Framework\TestCase;
use server\compute\compute;
class ComputeTest extends TestCase {
	public function testgetnearstops(){
		$res = [];
    	array_push($res, '5325','142356','1245','1245','42345');
    	$this->assertEquals($res,getNearStops());
    }
    public function testgetNextTime(){
    	$S='235';
    	$E='2321';
    	$l='safr';
    	$r='adsfg';
    	$this->assertEquals($E,getTimeAndInterval());
    	$this->assertEquals($S,getTimeAndInterval());
    	$this->assertEquals($l,getTimeAndInterval());
    	$this->assertEquals($r,getTimeAndInterval());
	}
	public function testgettimeandinterval(){
		$res = [];
    	array_push($res, '754');
    	$this->assertEquals($res,getNextTime());
    }
    public function testgetPathByPar(){
    	$res = [];
    	array_push($res, '542353');
    	$this->assertEquals($res,getPathByPar());
    }
    public function testDiikstra(){
        $res = array(
        "d" => "5233",
        "par" => "1241235",
        );
        $this->assertEquals($res,Diikstra()); 
    }
    public function testgetFastPaths(){
       $res = [];
        array_push($res, '645753');
        $this->assertEquals($res,getFastPaths()); 
    }
    public function testgetPaths(){
        $tmp = [];
        array_push($res, '8689');
        $this->assertEquals($tmp,getPaths());
    }