<?php
/**
 * Os Studios PagSeguro Payment Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   OsStudios
 * @package    OsStudios_PagSeguro
 * @copyright  Copyright (c) 2012 Os Studios (www.osstudios.com.br)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Tiago Sampaio <tiago.sampaio@osstudios.com.br>
 */

class OsStudios_PagSeguro_Model_Returns_Types_Api extends OsStudios_PagSeguro_Model_Returns_Types_Abstract
{
	
	/**
	 * 
	 * Handle the process result
	 * @var (bool)
	 */
	protected $_success = false;
	
	/**
	 * 
	 * Handle the response result
	 * @var (mixed)
	 */
	protected $_response = null;
	
	/**
	 * 
	 * Return true if the returned has processed
	 * @return (bool)
	 */
	public function isSuccess()
	{
		return $this->_success;
	}
	
	
	/**
	 * 
	 * Return response of the return
	 * @return (bool)
	 */
	public function getResponse()
	{
		return $this->_response;
	}
    
    
    /**
	 * 
	 * Process return
	 */
	public function processReturn()
	{
		return $this;
	}
    
}
