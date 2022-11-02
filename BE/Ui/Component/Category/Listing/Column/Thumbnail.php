<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Ui\Component\Category\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\UrlInterface;

class Thumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{
    protected $urlBuilder;

    const ALT_FIELD = 'name';
    const NAME = 'thumbnail';

    /**
     * Thumbnail constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            $path = $this->getBasePath();
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['category_image'])) {
                    $item[$fieldName . '_src'] = $path . $item['category_image'];
                    $item[$fieldName . '_alt'] = $this->getAlt($item) ?: '';
                    $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                        'ingredients/category/edit',
                        ['entity_id' => $item['entity_id']]
                    );
                    $item[$fieldName . '_orig_src'] = $path . $item['category_image'];
                }
            }
        }
        return $dataSource;
    }

    /**
     * @param $row
     * @return null
     */
    protected function getAlt($row)
    {

        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
        return isset($row[$altField]) ? $row[$altField] : null;
    }

    /**
     * @return string
     */
    protected function getBasePath()
    {
        return $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . 'ingredients/';
    }
}