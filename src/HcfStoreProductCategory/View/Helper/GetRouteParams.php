<?php
namespace HcfStoreProductCategory\View\Helper;

use HcBackend\Service\Alias\DetectAlias;
use HcbStoreProductCategory\Entity\Category as CategoryEntity;
use Zend\View\Helper\AbstractHelper;

class GetRouteParams extends AbstractHelper
{
    /**
     * @var DetectAlias
     */
    protected $fetchPrimaryAliasService;

    /**
     * @param DetectAlias $fetchPrimaryAliasService
     */
    public function __construct(DetectAlias $fetchPrimaryAliasService)
    {
        $this->fetchPrimaryAliasService = $fetchPrimaryAliasService;
    }

    /**
     * @param CategoryEntity $categoryEntity
     *
     * @return string
     */
    public function __invoke(CategoryEntity $categoryEntity)
    {
        $alias = $this->fetchPrimaryAliasService->detect($categoryEntity);
        return array('category'=>is_null($alias) ? $categoryEntity->getId() : $alias->getAlias()->getName());
    }
}
