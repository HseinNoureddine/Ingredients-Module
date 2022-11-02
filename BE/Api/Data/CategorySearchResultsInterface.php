<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Api\Data;

interface CategorySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \Custom\Ingredients\Api\Data\CategoryInterface[]
     */
    public function getItems();
    /**
     * @param \Custom\Ingredients\Api\Data\CategoryInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
