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

class OsStudios_PagSeguro_Model_Returns extends OsStudios_PagSeguro_Model_Abstract
{
	
	const PAGSEGURO_RETURN_RESPONSE_UNAUTHORIZED = 'Unauthorized';
	const PAGSEGURO_REUTRN_RESPONSE_AUTHORIZED = 'Authorized';
	const PAGSEGURO_RETURN_RESPONSE_ERROR = 'Process Error';
	
    /**
     * 
     * Default return from PagSeguro
     * @var (int)
     */
	const PAGSEGURO_RETURN_DEFAULT = 1;
	
	/**
     * 
     * Api return from PagSeguro
     * @var (int)
     */
	const PAGSEGURO_RETURN_API = 2;
	
	/**
     * 
     * Request a consult in PagSeguro
     * @var (int)
     */
	const PAGSEGURO_RETURN_CONSULT = 3;
	
	/**
	 * 
	 * Handle the return type
	 * @var (const)
	 */
	protected $_returnType = null;
	
	/**
	 * 
	 * Handle the post information
	 * @var (mixed)
	 */
	protected $_post = null;
	
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
	 * Sets the post data
	 * @param (mixed) $post
	 */
	public function setPostData($post)
	{
		$this->_post = $post;
		$this->setPost($this->_post);
		return $this;
	}
	
	
	/**
	 * 
	 * Sets the return type
	 * @param unknown_type $type
	 */
	public function setReturnType($type = self::PAGSEGURO_RETURN_DEFAULT)
	{
		$this->_returnType = $type;
		return $this;
	}
	
	
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
	 * Identifies and process the correct return
	 */
	public function runReturns()
	{
		$type = $this->_returnType;
		
		switch ($type)
		{
			case self::PAGSEGURO_RETURN_API:
				break;
				
			case self::PAGSEGURO_RETURN_CONSULT:
				
				$stop = false;
				
				$this->_response = Mage::getModel('pagseguro/returns_types_consult')->processReturn()->getResponse();
				if($this->_response == self::PAGSEGURO_RETURN_RESPONSE_UNAUTHORIZED)
				{
					$errMsg = $this->__('The consult was unauthorized by PagSeguro.');
					$stop = true;
				} elseif ($this->_response == self::PAGSEGURO_RETURN_RESPONSE_ERROR) {
					$errMsg = $this->__('PagSeguro has returned an error.');
					$stop = true;
				}
				
				if(Mage::getSingleton('admin/session') && $stop) {
					Mage::getSingleton('adminhtml/session')->addError($errMsg);
					$this->_redirect('pagseguro/adminhtml_index/index');
					return;
				}
				
				break;
				
			case self::PAGSEGURO_RETURN_DEFAULT:
			default:
				break;
		}
		
	}
	
}
