<?php
namespace Digitalradium\BestsellerSlider\Block;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\CategoryFactory;
use Digitalradium\BestsellerSlider\Helper\Data;

class OnSale extends Template
{
    protected $productCollectionFactory;
    protected $categoryFactory;
    protected $helper;

    public function __construct(
        Template\Context $context,
        CollectionFactory $productCollectionFactory,
        CategoryFactory $categoryFactory,
        Data $helper,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryFactory = $categoryFactory;
         $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function getProducts()
    {
        $categoryId = $this->getData('category_id');
        $limit = $this->getData('limit') ?? 8;
      
        return  $this->helper->getOnSaleProducts($limit, $categoryId);
    }
}
