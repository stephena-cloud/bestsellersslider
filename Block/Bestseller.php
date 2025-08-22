<?php
namespace Digitalradium\BestsellerSlider\Block;

use Magento\Framework\View\Element\Template;
use Digitalradium\BestsellerSlider\Helper\Data;

class Bestseller extends Template
{
    protected $helper;
    

    public function __construct(
        Template\Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function getProducts()
    {
        $categoryId = $this->getData('category_id');
        $limit = $this->getData('limit') ?? 8;
        return $this->helper->getBestsellingProducts($limit, $categoryId);
    }

    public function getCurrencySymbol()
    {
        return $this->_storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();
    }

    public function getProductImageUrl($product)
    {
        return $this->getUrl('pub/media/catalog/product') . $product->getImage();
    }
}
