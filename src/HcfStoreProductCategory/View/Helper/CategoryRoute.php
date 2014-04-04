<?php
namespace HcfStoreProductCategory\View\Helper;

use HcbStoreProduct\Entity\Product\Localized as LocalizedProduct;
use HcbStoreProductCategory\Entity\Category\Localized as LocalizedCategory;
use Zend\View\Helper\AbstractHelper;

class CategoryRoute extends AbstractHelper
{
    /**
     * @param LocalizedCategory $localizedCategory
     *
     * @return string
     */
    public function __invoke(LocalizedCategory $localizedCategory)
    {
        return 'category_'.$localizedCategory->getId();
    }
}
