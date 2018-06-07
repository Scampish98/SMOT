<?php
namespace tests;
use PHPUnit\Framework\TestCase;
use server\main;
class MainTest extends TestCase {
	/**
 	* @test
 	*/
 		public function handlerResponseShouldBe(){
    	$path = [];
    	array_push($path, '123','123');
    	$this->assertEquals($path,json_decode($this->handlerPaths());
 	 	$res = [];
    	array_push($res, '123','123');
    	$this->assertEquals($res,json_decode($this->handlerStopInfo());
		$res = [];
    	array_push($res, '123','123');
    	$this->assertEquals($res,json_decode($this->handlerRouteInfo());
    	$res = [];
    	array_push($res, '123','123');
    	$this->assertEquals($res,json_decode($this->handlerRoutes());
    	$res = [];
    	array_push($res, '123','123');
    	$this->assertEquals($res,json_decode($this->handlerStops());
 	}
}
