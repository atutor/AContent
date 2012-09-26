<?php

/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2010                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/



if (!defined('TR_INCLUDE_PATH')) exit;

class GoalsManager
{
	
	var $info;
	
	var $path;
	
	var $types;
	
	/* constructor	*/
	function GoalsManager() {
		$this->path = realpath(TR_INCLUDE_PATH. '../templates').'/goals/goals.info';
		if(!is_file($this->path)) 
			throw new Exception("Error: file \"goals.info\" not found!");
		else
			$this->setGoals();
		
	}
	
	function setGoals() {
		
		$this->info	= parse_ini_file($this->path);
		$this->types = $this->info['type'];
		
		foreach($this->types as $type) {
		
			$string = $this->info[$type];
			$temp_goals = explode(", ", $string);
			foreach ($temp_goals as $goal) 
				$this->goals[] = $goal;
				
		}
		
			
	}
	
	function getType($goal) {
		
		foreach ($this->types as $type) {
			$goalsByType = $this->getGoalsByType($type);
			foreach ($goalsByType as $g) {
				
				if($g == $goal) 
					return $type;
			}
		}
			
		return null;
	}

	/*function getStruct($structs) {
		$count = count($structs);
		if($count == 1)
			return $structs;
		else if($count == count($this->types)) {
			$result = '';
			for ($i=1; $i<=$count; $i++) {
				if($i == $count)
					$result .= $struct;
				else
					$result .= $struct .'_';
			}
			
		} else
			
	}*/
	
	function getGoalsByType($type) {
		return explode(', ', $this->info[$type]);
	}
		
	
	function getGoals() {
		
		return $this->goals;
	}
	
	
}
?>