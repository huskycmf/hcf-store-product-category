<?php
namespace HcfStoreProductCategory\View\Helper;

use HcbStoreProductCategory\Entity\Category as CategoryEntity;
use Zend\View\Helper\AbstractHelper;

class CategoryUrl extends AbstractHelper
{
    /**
     * @param CategoryEntity $categoryEntity
     *
     * @return string
     */
    public function __invoke(CategoryEntity $categoryEntity)
    {
        return $this->view->url('hc-frontend/category',
                                $this->view->categoryGetRouteParams($categoryEntity));
    }
}
