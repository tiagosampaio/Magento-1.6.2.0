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

class OsStudios_PagSeguro_Model_Returns_Transactions extends OsStudios_PagSeguro_Model_Returns_Abstract
{
    
	/**
	 * 
	 * 
	 * @var unknown_type
	 */
    protected $_transactionSearchResult = null;
    
    /**
     * 
     * Date of the inquiry
     * @var (string)
     */
    protected $_date = null;
    
    /**
     * 
     * Current page in the result
     * @var (int)
     */
    protected $_currentPage = 1;
    
    /**
     * 
     * Number of results in current page
     * @var (int)
     */
	protected $_resultsInThisPage = 0;
	
	/**
	 * 
	 * Number of pages in the result
	 * @var (int)
	 */
	protected $_totalPages = 1;
	
	
    
}
