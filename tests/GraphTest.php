<?php
namespace tests;
use PHPUnit\Framework\TestCase;
use server\compute\buildGraph;
class GraphTest extends TestCase {
 		public function graphshouldbe(){
    	require(buildGraph.php)
        $this->assertFileEquals('../expected', '../actual');
 	}
}