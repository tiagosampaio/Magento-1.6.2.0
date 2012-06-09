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
 * PagSeguro Installments Source
 *
 */

class OsStudios_PagSeguro_Model_System_Source_Installments
{
	public function toOptionArray ()
	{
		$options = array();
        
        $options[] 	= Mage::helper('adminhtml')->__('Deactivated (1x)');
        
        for($y=2; $y<=18; $y++){
        	$options[] 	= Mage::helper('adminhtml')->__("Max {$y}x");
        }
        /*
        $options[] 	= Mage::helper('adminhtml')->__('Até 2x');
        $options[] 	= Mage::helper('adminhtml')->__('Até 3x');
        $options[] 	= Mage::helper('adminhtml')->__('Até 4x');
        $options[] 	= Mage::helper('adminhtml')->__('Até 5x');
        $options[] 	= Mage::helper('adminhtml')->__('Até 6x');
        $options[] 	= Mage::helper('adminhtml')->__('Até 7x');
        $options[] 	= Mage::helper('adminhtml')->__('Até 8x');
        $options[] 	= Mage::helper('adminhtml')->__('Até 9x');
        $options[] 	= Mage::helper('adminhtml')->__('Até 10x');
        $options[] 	= Mage::helper('adminhtml')->__('Até 11x');
        $options[] 	= Mage::helper('adminhtml')->__('Até 12x');
        $options[] 	= Mage::helper('adminhtml')->__('Até 13x');
        $options[] 	= Mage::helper('adminhtml')->__('Até 14x');
        $options[] 	= Mage::helper('adminhtml')->__('Até 15x');
        $options[] 	= Mage::helper('adminhtml')->__('Até 16x');
        $options[] 	= Mage::helper('adminhtml')->__('Até 17x');
        $options[] 	= Mage::helper('adminhtml')->__('Até 18x');
        */
		return $options;
	}

}