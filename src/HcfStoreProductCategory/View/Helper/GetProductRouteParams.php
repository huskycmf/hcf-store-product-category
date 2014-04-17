<?php
namespace HcfStoreProductCategory\View\Helper;

use HcBackend\Service\Alias\DetectAlias;
use HcbStoreProduct\Entity\Product as ProductEntity;
use Zend\View\Helper\AbstractHelper;

class GetProductRouteParams extends AbstractHelper
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
     * @param ProductEntity $productEntity
     *
     * @return string
     */
    public function __invoke(ProductEntity $productEntity)
    {
        $alias = $this->fetchPrimaryAliasService->detect($productEntity);
        return array('product'=>is_null($alias) ? $productEntity->getId() : $alias->getAlias()->getName());
    }
}
