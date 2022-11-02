<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Model\Ingredient\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Option\ArrayInterface;

class CategorySelection extends  AbstractSource implements ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    protected $categoryCollection;
    /**
     *
     * @param CollectionFactory $categoryCollection
     *
     * @return $this
     */
    public function __construct(
        \Custom\Ingredients\Model\ResourceModel\Category\CollectionFactory $categoryCollection
    ) {
        $this->categoryCollection = $categoryCollection;
    }

    public function toOptionArray($addEmpty = true)
    {
        $options = [];

        if ($addEmpty) {
            $options[] = ['label' => __('-- Please Select a Category'), 'value' => ''];
        }

        foreach ($this->getCollection() as $item) {
            $options[] = [
                'label' => $item->getCategoryName(),
                'value' => $item->getId(),
            ];
        }

        return $options;
    }

    public function getAllOptions()
    {
        $this->_options = [];
        $collection = $this->getCollection();

        if (!$this->_options) {
            if ($collection) {
                foreach ($this->getCollection() as $item) {
                    $this->_options[] = [
                        'label' => $item->getCategoryName(),
                        'value' => $item->getId(),
                    ];
                }
            } else {
                $this->_options[] = ['label' => (''), 'value' => ''];
            }
        }
        return $this->_options;
    }

    private function getCollection()
    {
        return $this->categoryCollection->create()
            ->addAttributeToSelect(['entity_id', 'category_name'])
            ->setOrder('category_name', 'ASC');
    }
}
