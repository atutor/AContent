<?php
/************************************************************************/
/* AContent                                                         */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

/**
* RESTWebServiceOutput
* Class to generate search or error report in REST format 
* @access	public
* @author	Cindy Qi Li
*/
if (!defined("TR_INCLUDE_PATH")) die("Error: TR_INCLUDE_PATH is not defined.");

class RESTWebServiceOutput {

	// all private
	var $results;                    // constructor parameter. array of errors
	var $totalCount;                 // constructor parameter. total number of search results regardless of the maxResults
	var $lastRecNumber;              // constructor parameter. number of the last record in the <results> element
	var $output;                     // final web service output
	
	// REST templates
	var $rest_main =
'<?xml version="1.0" encoding="ISO-8859-1"?>
<resultset>
  <summary>
    <numOfTotalResults>{NUMOFTOTALRESULTS}</numOfTotalResults>
    <numOfResults>{NUMOFRESULTS}</numOfResults>
    <lastResultNumber>{LASTRESULTNUMBER}</lastResultNumber>
  </summary>

  <results>
{RESULTS}
  </results>
</resultset>
';
	
	var $rest_result = 
'    <result>
      <courseID>{COURSEID}</courseID>
      <title>{TITLE}</title>
      <description>{DESCRIPTION}</description>
      <creationDate>{CREATIONDATE}</creationDate>
    </result> 
';
	
	/**
	* class constructor
	* @access public
	* @param  $results: an array of search results
	*         $totalCount: total number of all search results
	*         $lastRecNumber: number of the last record in the <results> element
	*/
	function RESTWebServiceOutput($results, $totalCount, $lastRecNumber)
	{
		$this->results = $results;
		$this->totalCount = $totalCount;
		$this->lastRecNumber = $lastRecNumber;
		
		$this->generateRESTRpt();
	}
	
	/**
	* private
	* main process to generate report in html format
	*/
	private function generateRESTRpt()
	{
		if (!is_array($this->results))
		{
			$num_of_results = 0;
			$results_in_rest = '';
		}
		else
		{
			$num_of_results = count($this->results);
			foreach ($this->results as $result)
			{
				$results_in_rest .= str_replace(array('{COURSEID}', 
				                             '{TITLE}',
				                             '{DESCRIPTION}', 
				                             '{CREATIONDATE}'),
				                      array($result['course_id'], 
				                            $result['title'], 
				                            $result['description'], 
				                            $result['created_date']),
				                      $this->rest_result);
			}
		}
		
		// calculate the last record number
		
		// generate final output
		$this->output = str_replace(array('{NUMOFTOTALRESULTS}', 
		                                  '{NUMOFRESULTS}', 
				                          '{LASTRESULTNUMBER}', 
		                                  '{RESULTS}'),
		                            array($this->totalCount,
		                                  $num_of_results,
		                                  $this->lastRecNumber,
		                                  $results_in_rest), 
		                            $this->rest_main);
	}
	
	/** 
	* public
	* return final web service output
	* parameters: none
	* author: Cindy Qi Li
	*/
	public function getWebServiceOutput()
	{
		return $this->output;
	}
	
	/** 
	* public
	* return error report in html
	* parameters: $errors: errors array
	* author: Cindy Qi Li
	*/
	public static function generateErrorRpt($errors)
	{
		// initialize error codes. Note that all errors reported in REST need to be defined here.
		$errorCodes['TR_ERROR_EMPTY_KEYWORDS'] = 401;
		$errorCodes['TR_ERROR_EMPTY_WEB_SERVICE_ID'] = 402;
		$errorCodes['TR_ERROR_INVALID_WEB_SERVICE_ID'] = 403;
		
		// error template in REST format
		$rest_error = 
'<?xml version="1.0" encoding="ISO-8859-1"?>
<errors>
  <totalCount>{TOTOAL_COUNT}</totalCount>
{ERROR_DETAIL}
</errors>
';
	
		$rest_error_detail = 
'  <error code="{ERROR_CODE}">
    <message>{MESSAGE}</message>
  </error>
';
		if (!is_array($errors)) return false;
		
		foreach ($errors as $err)
		{
			$error_detail .= str_replace(array("{ERROR_CODE}", "{MESSAGE}"), 
			                             array($errorCodes[$err], htmlentities(_AT($err))), 
			                             $rest_error_detail); 
		}
			                            
		return str_replace(array('{TOTOAL_COUNT}', '{ERROR_DETAIL}'), 
		                   array(count($errors), $error_detail),
		                   $rest_error);
	}
}
?>  
