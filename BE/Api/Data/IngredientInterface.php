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

interface IngredientInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    const ENTITY                   = 'custom_ingredient';
    const ID                       = 'entity_id';
    const INGREDIENT_NAME          = 'ingredient_name';
    const LETTER                   = 'letter';
    const IS_ACTIVE                = 'is_active';
    const URL_KEY                  = 'url_key';
    const INGREDIENT_IMAGE         = 'ingredient_image';
    const DESCRIPTION              = 'description';
    const FEATURED                 = 'featured';
    const META_TITLE               = 'meta_title';
    const META_KEYWORDS            = 'meta_keywords';
    const META_DESCRIPTION         = 'meta_description';

    const PRODUCT_IDS              = 'product_ids';
    const CATEGORY_ID              = 'category_id';
    const INGREDIENT_IDS           = 'ingredient_ids';

    public function getId();

    public function setId($id);

    public function getIngredientName();

    public function setIngredientName($ingredientName);

    public function getLetter();

    public function setLetter($letter);

    public function setIsActive($status);

    public function getIsActive();

    public function getUrlKey();

    public function setUrlKey($urlKey);

    public function getIngredientImage();

    public function setIngredientImage($ingredientImage);

    public function getDescription();

    public function setDescription($description);

    public function isFeatured();

    public function getMetaTitle();

    public function setMetaTitle($metaTitle);

    public function getMetaKeywords();

    public function setMetaKeywords($metaKeywords);

    public function getMetaDescription();

    public function setMetaDescription($metaDescription);

    public function getProductIds();

    public function setProductIds(array $productIds);

    public function getIngredientProducts();

    public function getIngredientIds();

    public function setIngredientIds(array $ingredientIds);

    public function getCategoryId();

    public function setCategoryId($categoryId);

    public function getProductIngredients($product);
}
