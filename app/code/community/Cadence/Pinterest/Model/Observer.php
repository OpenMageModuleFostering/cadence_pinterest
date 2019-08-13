<?php
/**
 * @author Alan Barber <alan@cadence-labs.com>
 */
Class Cadence_Pinterest_Model_Observer
{
    /**
     * @param Varien_Event_Observer $obs
     * @return $this
     */
    public function onSalesQuoteProductAddAfter(Varien_Event_Observer $obs)
    {
        if (!$this->_helper()->isAddToCartPixelEnabled()) {
            return $this;
        }

        $items = $obs->getItems();

        $candidates = array_replace(array(
            'value' => 0.00,
            'order_quantity' => 0,
            'line_items' => array()
        ), $this->_getSession()->getAddToCart() ?: array());

        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $candidates['value'] += $item->getProduct()->getFinalPrice() * $item->getProduct()->getQty();
            $candidates['order_quantity'] += $item->getProduct()->getQty();
            $candidates['line_items'][] = [
                "product_name" => $item->getName(),
                "product_id" => $item->getSku(),
                "product_price" => round($item->getProduct()->getFinalPrice(),2),
                "product_quantity" => max(round($item->getProduct()->getQty()), 1)
            ];
        }

        // Ensure the quantity is a whole integer
        $data = array(
            'value' => round($candidates['value'],2),
            'order_quantity' => max(round($candidates['order_quantity']), 1),
            'currency' => Mage::app()->getStore()->getCurrentCurrencyCode(),
            'line_items' => $candidates['line_items']
        );

        $this->_getSession()->setAddToCart($data);

        return $this;
    }

    /**
     * @return Cadence_Pinterest_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('cadence_pinterest/session');
    }

    /**
     * @return Cadence_Pinterest_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper("cadence_pinterest");
    }
}