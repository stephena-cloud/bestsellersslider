<?php
namespace Digitalradium\BestsellerSlider\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\App\CacheInterface;

use Zend_Db_Expr as Expr;

class Data extends AbstractHelper
{
    protected ProductCollectionFactory $productCollectionFactory;
    protected ResourceConnection $resource;
    protected CategoryFactory $categoryFactory;
    protected CacheInterface $cache;

    // Cache lifetime in seconds (7 days)
    private const CACHE_LIFETIME = 604800;

    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        ResourceConnection $resource,
        CategoryFactory $categoryFactory,
        CacheInterface $cache
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->resource = $resource;
        $this->categoryFactory = $categoryFactory;
        $this->cache = $cache;
    }

    /**
     * Get bestselling products within a category
     */
    public function getBestsellingProducts($limit = 8, $categoryId = null)
    {
        $limit = $limit != "" ? $limit : 8;
        var_dump("getBestsellingProducts", $limit);
        $cacheKey = 'bestseller_products_' . $limit . '_' . (int)$categoryId;

        // 1. Try cache first
        if ($cachedIds = $this->cache->load($cacheKey)) {
            $bestsellerProductIds = json_decode($cachedIds, true);
        } else {
            $connection = $this->resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);

            // Get product IDs in the category
            $categoryProductIds = [];
            if ($categoryId) {
                $category = $this->categoryFactory->create()->load($categoryId);
                if ($category->getId()) {
                    $categoryProductIds = $category->getProductCollection()
                        ->addAttributeToFilter('status', Status::STATUS_ENABLED)
                        ->getAllIds();
                }
            }

            if (empty($categoryProductIds)) {
                return [];
            }

            // Query top-selling products
            $salesOrderItem = $connection->getTableName('sales_order_item');
            $select = $connection->select()
                ->from(['soi' => $salesOrderItem], [
                    'product_id',
                    new Expr('SUM(soi.qty_ordered) as qty_ordered')
                ])
                ->where('soi.product_id IN (?)', $categoryProductIds)
                ->group('soi.product_id')
                ->order('qty_ordered DESC')
                ->limit($limit);

            try {
                $results = $connection->fetchAll($select);
                $bestsellerProductIds = array_column($results, 'product_id');

                // Save to cache
                $this->cache->save(
                    json_encode($bestsellerProductIds),
                    $cacheKey,
                    [],
                    self::CACHE_LIFETIME
                );
            } catch (\Exception $e) {
                return [];
            }
        }

        if (empty($bestsellerProductIds)) {
            return [];
        }

        // Load product collection
        $collection = $this->productCollectionFactory->create()
            ->addAttributeToSelect(['name', 'price', 'special_price', 'thumbnail', 'url_key'])
            ->addAttributeToFilter('status', Status::STATUS_ENABLED)
            ->addAttributeToFilter('entity_id', ['in' => $bestsellerProductIds])
            ->setPageSize($limit)
            ->setCurPage(1);

        // Preserve bestseller order
        $collection->getSelect()->order(
            new Expr('FIELD(e.entity_id, ' . implode(',', $bestsellerProductIds) . ')')
        );

        return $collection;
    }

    /**
     * Get on-sale products (special price active)
     */
    public function getOnSaleProducts($limit = 8, $categoryId = null)
    {
        var_dump("getBestsellingProducts", $limit);
        $cacheKey = 'onsale_products_' . $limit . '_' . (int)$categoryId;

        $limit = $limit != "" ? $limit : 8;

        // 1. Try cache first
        if ($cachedIds = $this->cache->load($cacheKey)) {
            $onSaleProductIds = json_decode($cachedIds, true);
        } else {
            $today = date('Y-m-d H:i:s');

            $collection = $this->productCollectionFactory->create()
                ->addAttributeToSelect(['entity_id'])
                ->addAttributeToFilter('status', Status::STATUS_ENABLED)
                ->addAttributeToFilter('special_price', ['neq' => ''])
                ->addAttributeToFilter(
                    [
                        ['attribute' => 'special_from_date', 'null' => true],
                        ['attribute' => 'special_from_date', 'lteq' => $today]
                    ],
                    null,
                    'left'
                )
                ->addAttributeToFilter(
                    [
                        ['attribute' => 'special_to_date', 'null' => true],
                        ['attribute' => 'special_to_date', 'gteq' => $today]
                    ],
                    null,
                    'left'
                )
                ->setPageSize($limit)
                ->setCurPage(1);

            if ($categoryId) {
                $category = $this->categoryFactory->create()->load($categoryId);
                if ($category->getId()) {
                    $collection->addCategoriesFilter(['in' => [$category->getId()]]);
                }
            }

            $onSaleProductIds = $collection->getAllIds();

            // Save to cache
            $this->cache->save(
                json_encode($onSaleProductIds),
                $cacheKey,
                [],
                self::CACHE_LIFETIME
            );
        }

        if (empty($onSaleProductIds)) {
            return [];
        }

        // Reload actual product collection with required attributes
        $collection = $this->productCollectionFactory->create()
            ->addAttributeToSelect(['name', 'price', 'special_price', 'thumbnail', 'url_key'])
            ->addAttributeToFilter('status', Status::STATUS_ENABLED)
            ->addAttributeToFilter('entity_id', ['in' => $onSaleProductIds])
            ->setPageSize($limit)
            ->setCurPage(1);

        return $collection;
    }
}
