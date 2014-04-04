<?php
namespace HcfStoreProductCategory\View\Helper;

use HcbStoreProduct\Entity\Product\Localized as LocalizedProduct;
use HcbStoreProductCategory\Entity\Category\Localized as LocalizedCategory;
use Zend\View\Helper\AbstractHelper;

class CategoryProductUrl extends AbstractHelper
{
    /**
     * @param LocalizedCategory $localizedCategory
     * @param LocalizedProduct $localizedProductEntity
     *
     * @return string
     */
    public function __invoke(LocalizedCategory $localizedCategory, LocalizedProduct $localizedProductEntity)
    {
        return $this->getView()->url('category_'.$localizedCategory->getId().
                                     '/product_'.$localizedProductEntity->getId());
    }
}
