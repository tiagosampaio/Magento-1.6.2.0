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
 * @category   payment
 * @package    OsStudios_PagSeguro
 * @copyright  Copyright (c) 2012 Os Studios (www.osstudios.com.br)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Tiago Sampaio <tiago.sampaio@osstudios.com.br>
 */

/**
 * PagSeguro Shipping Price Source
 *
 */

class OsStudios_PagSeguro_Model_System_Source_Shipping
{
	public function toOptionArray ()
	{
		$options = array();
        
        $options['separated'] 	= Mage::helper('pagseguro')->__('Separated');
        $options['product'] 	= Mage::helper('pagseguro')->__('As Product');
        $options['grouped'] 	= Mage::helper('pagseguro')->__('Grouped');
        
		return $options;
	}

}