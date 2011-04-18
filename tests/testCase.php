<?php
require_once(dirname(__FILE__).'/../dbStruct.php');
class dbStructTest extends PHPUnit_Framework_TestCase
{
	// strings used for the test along with the right answers
	protected $delimPosSnippets = array(
			// test string
			'Select * FROM `posts` where id=20;',
			// number(s) of the proper delimiter positions (comma-separated)
			'1',
			'where tags like "%weather;news%";',
			'2',
			'string without -- delimiter at all',
			'',
			'string with; the delimiter -- in the; comment',
			'1',
			"string with the delimiter ';in single; quotes;';",
			'4'
		);	
	
	public function testGetDelimPos()
	{
		// initialize testing object (with needed params)
		$struct = new dbStructUpdater();
		// get testing snippets, alter them if needed
		$snippets = $this->delimPosSnippets;
		$this->delimPosHelper($struct, $snippets, 'getDelimPos');
	}
	
	public function testGetDelimRpos()
	{
		// initialize testing object (with needed params)
		$struct = new dbStructUpdater();
		// get testing snippets, alter them if needed
		$snippets = $this->delimPosSnippets;
		$this->delimPosHelper($struct, $snippets, 'getDelimRpos');
	}
	
	public function testProcessLine()
	{
		$snippets = array(
			'CREATE TABLE IF NOT EXISTS `cnt_viewClasses` (',
			FALSE,
			'`id` int(11) NOT NULL AUTO_INCREMENT,',
			array('!`id`'=>'`id` int(11) NOT NULL AUTO_INCREMENT'),
			"  `authorId` varchar(50) NOT NULL DEFAULT '',",
			array('!`authorid`'=>'`authorId` varchar(50) NOT NULL'),
			"  `modified` bigint(14) NOT NULL DEFAULT '0',",
			array('!`modified`'=>'`modified` bigint(14) NOT NULL'),
			"`default_readRights` varchar(30) NOT NULL DEFAULT 'inheritFromViewClass',",
			array('!`default_readrights`'=>"`default_readRights` varchar(30) NOT NULL DEFAULT 'inheritFromViewClass'"),
			" PRIMARY  KEY (`id`),",
			array('primary key'=>'PRIMARY  KEY (`id`)'),
			"KEY `identity` (`firstname`, `lastname`)",
			array('key `identity`'=>"KEY `identity` (`firstname`, `lastname`)"),
			") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;",
			FALSE
		);
		
		$struct = new dbStructUpdater();
		foreach($snippets as $k=>$snippet)
		{
			if(!is_string($snippet)) continue;
			$data = $struct->processLine($snippet);
			$expected = is_array($snippets[$k+1]) ? array('key' => reset(array_keys($snippets[$k+1])), 'line' => reset($snippets[$k+1])) : $snippets[$k+1];
			$this->assertEquals($expected, $data);
		}
	}
	
	public function testSplitTabSql()
	{
		$struct = new dbStructUpdater();
		$snippets = array(
			$this->getFileContent('viewClasses.sql'),
			include('viewClassesSplit.php'),
		);
		$snippetsCount = 0;
		foreach($snippets as $k=>$snippet)
		{
			if (!is_string($snippet)) continue;
			$ar = $struct->splitTabSql($snippet);
			$this->assertEquals($snippets[$k+1], $ar);
			$snippetsCount++;
		}
		$this->assertEquals(count($snippets)/2, $snippetsCount, 'please re-check test snippets');
	}

	public function testCompareSql()
	{
		$struct = new dbStructUpdater();
		$snippets = array(
			$this->getFileContent('compareSql_1_left.sql'),
			$this->getFileContent('compareSql_1_right.sql'),
			array(),
			//--- data for the next test goes below ---//
		);
		
		for($k=0; $k<count($snippets); $k+=3)
		{
			$left = $snippets[$k];
			$right = $snippets[$k+1];
			$expected = $snippets[$k+2];

			$result = $struct->compareSql($left, $right);
			$this->assertEquals($expected, $result);
		}
	}

	public function testGetUpdates()
	{
		$struct = new dbStructUpdater();
		$snippets = array(
			$this->getFileContent('left_1.sql'),
			$this->getFileContent('right_1.sql'),
			array_map('rtrim', $this->getFileContent('update_1.sql', true)),
			//--- data for the next test goes below ---//
		);
		for($k=0; $k<count($snippets); $k+=3)
		{
			$left = $snippets[$k];
			$right = $snippets[$k+1];
			$expected = $snippets[$k+2];

			$result = $struct->getUpdates($left, $right);
			$this->assertEquals($expected, $result);
		}
	}

	protected function delimPosHelper($struct, $snippets, $posMethod)
	{
		foreach($snippets as $k=>$snippet)
		{
			// continue if that's not a snippet element
			if ( !(($k+1)%2) ) continue;
			$originalSnippet = $snippet;
			// get all the delimiter positions using strpos
			$allPositions = $this->getAllDelimPositions(';', $snippet);
			$rightPositionNumbers = explode(',', $snippets[$k+1]);
			// find out only right delimiter positions
			$rightPositions = array();
			foreach($rightPositionNumbers as $num)
			{
				if ($num==='') continue;
				$this->assertTrue(isset($allPositions[$num-1]), 're-check test data - imporoper position number for snippet #'.($k/2+1));
				$rightPositions[] = $allPositions[$num-1];
			}
			// get delimter positions detected by the tested class
			$detectedPositions = array();
			$offset = 0;
			while(true)
			{
				$detectedPosition = $struct->$posMethod($snippet, $offset);
				if ($detectedPosition === FALSE) break;
				if ($posMethod=='getDelimPos')
				{
					// increase offset
					$offset = $detectedPosition + 1;
				}
				//getDelimRpos
				else 
				{
					// rtrim the snippet by the found delimiter
					$snippet = substr($snippet, 0, $detectedPosition);
				}
				$detectedPositions[] = $detectedPosition; 
			}
			// make sure positions are the same in both cases
			$this->assertEquals($rightPositions, $detectedPositions, 'Failed locating delimiter in the snippet: '.$originalSnippet);
		}
	}

	/**
	* Used by testGetDelimPos to detect all the positions of the delimiter
	* in the given string using strpos()
	*/
	protected function getAllDelimPositions($delim, $string)
	{
		$positions = array();
		$offset = 0;
		while(true)
		{
			$pos = strpos($string, $delim, $offset);
			if ($pos === FALSE) break;
			$positions[] = $pos;
			$offset = $pos+1;
		}
		return $positions;
	}
	
	protected function getFileContent($file, $asArray=false)
	{
		$path = dirname(__FILE__).'/'.$file;
		if ($asArray) return file($path);
		return file_get_contents($path);
	}
}
