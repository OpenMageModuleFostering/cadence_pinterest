<?php
/**
 * @author Alan Barber <alan@cadence-labs.com>
 */ 
class Cadence_Pinterest_Helper_Data extends Mage_Core_Helper_Abstract 
{
    protected $_order;

    public function isVisitorPixelEnabled()
    {
        return Mage::getStoreConfig("cadence_pinterest/visitor/enabled");
    }

    public function isConversionPixelEnabled()
    {
        return Mage::getStoreConfig("cadence_pinterest/conversion/enabled");
    }

    public function isAddToCartPixelEnabled()
    {
        return Mage::getStoreConfig("cadence_pinterest/add_to_cart/enabled");
    }

    public function getTagId()
    {
        return Mage::getStoreConfig("cadence_pinterest/visitor/tag_id");
    }

    /**
     * @param $event
     * @param $data
     * @return string
     */
    public function getPixelHtml($event, $data = false)
    {
        $json = '';
        if ($data) {
            $json = ', ' . json_encode($data);
        }
        $html = <<<HTML
    <!-- Begin Pinterest {$event} Pixel -->
    <script type="text/javascript">
        pintrk('track', '{$event}'{$json});
    </script>
    <!-- End Facebook {$event} Pixel -->
HTML;
        return $html;
    }

    public function getOrderIDs()
    {
        $orderIDs = array();

        foreach($this->_getOrder()->getAllVisibleItems() as $item){
            $product = Mage::getModel('catalog/product')->load( $item->getProductId() );
            $orderIDs = array_merge($orderIDs, $this->_getProductTrackID($product));
        }

        return json_encode($orderIDs);
    }

    protected function _getOrder(){
        if(!$this->_order){
            $orderId = Mage::getSingleton('checkout/type_onepage')->getCheckout()->getLastOrderId();
            $this->_order =  Mage::getModel('sales/order')->load($orderId);
        }

        return $this->_order;
    }

    protected function _getProductTrackID($product)
    {
        $productType = $product->getTypeID();

        if($productType == "grouped") {
            return $this->_getProductIDs($product);
        } else {
            return $this->_getProductID($product);
        }
    }

    protected function _getProductIDs($product)
    {
        $group = Mage::getModel('catalog/product_type_grouped')->setProduct($product);
        $group_collection = $group->getAssociatedProductCollection();
        $ids = array();

        foreach ($group_collection as $group_product) {

            $ids[] = $this->_getProductID($group_product);
        }

        return $ids;
    }

    protected function _getProductID($product)
    {
        return array(
            $product->getSku()
        );
    }

    public function getOrderItemsQty()
    {
        $order = $this->_getOrder();

        $qty = 0;

        /** @var Mage_Sales_Model_Order_Item $item */
        foreach($order->getAllVisibleItems() as $item) {
            $qty += $item->getQtyOrdered();
        }

        return max(round($qty), 1);
    }

    /**
     * @return string
     */
    public function getOrderItemsJson()
    {
        $order = $this->_getOrder();

        $itemData = array();

        /** @var Mage_Sales_Model_Order_Item $item */
        foreach($order->getAllVisibleItems() as $item) {
            $qty = max(round($item->getQtyOrdered()), 1);
            $itemData[] = [
                "product_name" => $item->getName(),
                "product_id" => $item->getSku(),
                "product_price" => round($item->getPrice(),2),
                "product_quantity" => $qty
            ];
        }

        return json_encode($itemData);
    }
}