<?php
namespace App\Tests\Entity;
 
use App\Entity\Team;
use PHPUnit\Framework\TestCase;
 
class TeamTest extends TestCase{
	public function testTeamGetterSetter(){
		$team = new Team();
		$team->setName('Name');
		$this->assertEquals("Name", $team->getName());
	}
}
