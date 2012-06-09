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
 * PagSeguro CMS Return Page Source
 *
 */

class OsStudios_PagSeguro_Model_System_Source_ReturnPage
{
	public function toOptionArray ()
	{
		$collection = Mage::getModel('cms/page')->getCollection();
		$pages = array();
		foreach ($collection as $page) {
			$pages[$page->getIdentifier()] = $page->getTitle();
		}
		return $pages;
	}

}