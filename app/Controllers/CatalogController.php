<?php
namespace Controllers;

use Models\GoodsList;

class CatalogController extends BaseController
{
    private int $goodsPerPage = 6;

    public function __construct($params)
    {
        parent::__construct($params);

        $this->title = 'Catalog';
        $this->templateFileName = 'catalog.html.twig';
        $catalogContent = [
            'catalogPath' => '/catalog',
            'goods' => $this->getGoodsList(),
            'pagesQuantity' => $this->getPagesQuantity(),
            'currentPage' => $this->getCurrentPage(),
        ];
        foreach ($catalogContent as $fieldName => $fieldValue) {
            $this->addContent($fieldName, $fieldValue);
        }
    }

    private function getGoodsList()
    {
        $startId = (($this->getCurrentPage() - 1) * $this->goodsPerPage) + 1;
        return GoodsList::some($startId, $this->goodsPerPage);
    }

    private function getPagesQuantity(): int
    {
        return (int) ceil(GoodsList::allQuantity() / $this->goodsPerPage);
    }

    private function getCurrentPage(): int
    {
        if (isset($_GET['page'])) {
            return (int) $_GET['page'];
        } else {
            return 1;
        }
    }

    function beforeRender() {}
}
