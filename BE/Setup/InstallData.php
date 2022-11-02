<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Ingredient setup factory
     *
     * @var IngredientSetupFactory
     */
    protected $ingredientSetupFactory;

    /**
     * Ingredient Category setup factory
     *
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;
    /**
     * Init
     *
     * @param IngredientSetupFactory $ingredientSetupFactory
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        IngredientSetupFactory $ingredientSetupFactory,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->ingredientSetupFactory = $ingredientSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) //@codingStandardsIgnoreLine
    {
        $setup->startSetup();
        /** @var IngredientSetup $ingredientSetupFactory */
        $ingredientSetup = $this->ingredientSetupFactory->create(['setup' => $setup]);
        $ingredientSetup->installEntities();

        $ingredientEntities = $ingredientSetup->getDefaultEntities();
        foreach ($ingredientEntities as $entityName => $entity) {
            $ingredientSetup->addEntityType($entityName, $entity);
        }

        /** @var CategorySetup $categorySetupFactory */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $categorySetup->installEntities();

        $categoryEntities = $categorySetup->getDefaultEntities();
        foreach ($categoryEntities as $entityName => $entity) {
            $categorySetup->addEntityType($entityName, $entity);
        }

        $setup->endSetup();
    }
}
