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

use Magento\Framework\Api\CustomAttributesDataInterface;

interface CategoryInterface extends CustomAttributesDataInterface
{
    const ENTITY                   = 'custom_ingredient_category';
    const ID                       = 'entity_id';
    const CATEGORY_NAME            = 'category_name';
    const URL_KEY                  = 'url_key';
    const POSITION                 = 'position';
    const CATEGORY_IMAGE           = 'category_image';

    public function getCategoryName();

    public function setCategoryName($categoryName);

    public function getUrlKey();

    public function setUrlKey($urlKey);

    public function getPosition();

    public function setPosition($position);

    public function getCategoryImage();

    public function setCategoryImage($image);
}
