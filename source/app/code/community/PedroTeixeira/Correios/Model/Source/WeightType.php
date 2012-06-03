<?php
/**
 * Pedro Teixeira
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the New BSD License.
 * It is also available through the world-wide-web at this URL:
 * http://www.pteixeira.com.br/new-bsd-license/
 *
 * @category   PedroTeixeira
 * @package    PedroTeixeira_Correios
 * @copyright  Copyright (c) 2011 Pedro Teixeira (http://www.pteixeira.com.br)
 * @author     Pedro Teixeira <pedro@pteixeira.com.br>
 * @license    http://www.pteixeira.com.br/new-bsd-license/ New BSD License
 */

class PedroTeixeira_Correios_Model_Source_WeightType
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'gr', 'label'=>Mage::helper('adminhtml')->__('Gramas')),
            array('value'=>'kg', 'label'=>Mage::helper('adminhtml')->__('Kilos')),
        );
    }

}
