<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Ui\Component\Ingredient\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Custom\Ingredients\Api\Data\IngredientInterface;

/**
 * @api
 * @since 101.0.0
 */
class StatusText extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Source\Boolean
     *
     */
    private $status;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Boolean $status
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Boolean $status,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->status = $status;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @since 101.0.0
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        $fieldName = $this->getData('name');
        $sourceFieldName = IngredientInterface::IS_ACTIVE;

        foreach ($dataSource['data']['items'] as &$item) {
            if (!empty($item[$sourceFieldName])) {
                $item[$fieldName] = $this->status->getOptionText($item[$sourceFieldName]);
            }
        }

        return $dataSource;
    }
}
