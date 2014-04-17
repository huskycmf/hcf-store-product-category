<?php
namespace HcfStoreProductCategory\View\Helper;

use HcbStoreProduct\Entity\Product as ProductEntity;
use HcbStoreProductCategory\Entity\Category as CategoryEntity;
use Zend\View\Helper\AbstractHelper;

class CategoryProductUrl extends AbstractHelper
{
    /**
     * @param CategoryEntity $categoryEntity
     * @param ProductEntity $productEntity
     *
     * @return string
     */
    public function __invoke(CategoryEntity $categoryEntity, ProductEntity $productEntity)
    {
        return $this->getView()->url('hc-frontend/category/product',
                                     array_merge($this->view->categoryGetProductRouteParams($productEntity),
                                                 $this->view->categoryGetRouteParams($categoryEntity)));
    }
}
