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

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Custom\Ingredients\Setup\EavTablesSetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var EavTablesSetupFactory
     */
    protected $eavTablesSetupFactory;

    /**
     * Init
     *
     * @internal param EavTablesSetupFactory $eavTablesSetupFactory
     */
    public function __construct(EavTablesSetupFactory $eavTablesSetupFactory)
    {
        $this->eavTablesSetupFactory = $eavTablesSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $installer, ModuleContextInterface $context) //@codingStandardsIgnoreLine
    {
        $installer->startSetup();
        $this->installIngredientEntity($installer);
        $this->installCategoryEntity($installer);
        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function installIngredientEntity(SchemaSetupInterface $installer)
    {
        $tableName = IngredientSetup::ENTITY_TYPE_CODE . '_entity';
        $table = $installer->getConnection()
            ->newTable($installer->getTable($tableName))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )->setComment('Entity Table');
        $table->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Creation Time'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Update Time'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('custom_ingredient_product'))
            ->addColumn(
                'ingredient_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
                'Ingredient ID'
            )->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
                'Product ID'
            )->addColumn(
                'position',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true, 'default' => '0'],
                'Position'
            )->addIndex(
                $installer->getIdxName('custom_ingredient_product', ['ingredient_id']),
                ['ingredient_id']
            )->addIndex(
                $installer->getIdxName('custom_ingredient_product', ['product_id']),
                ['product_id']
            )->addForeignKey(
                $installer->getFkName(
                    'custom_ingredient_product',
                    'product_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'product_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    'custom_ingredient_product',
                    'ingredient_id',
                    'custom_ingredient_entity',
                    'entity_id'
                ),
                'ingredient_id',
                $installer->getTable('custom_ingredient_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )->setComment('Ingredient To Product Linkage Table');
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('custom_ingredient_ingredients_category'))
            ->addColumn(
                'ingredient_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
                'Ingredient ID'
            )->addColumn(
                'category_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
                'Category ID'
            )->addColumn(
                'position',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true, 'default' => '0'],
                'Position'
            )->addIndex(
                $installer->getIdxName('custom_ingredient_ingredients_category', ['ingredient_id']),
                ['ingredient_id']
            )->addIndex(
                $installer->getIdxName('custom_ingredient_ingredients_category', ['category_id']),
                ['category_id']
            )->addForeignKey(
                $installer->getFkName(
                    'custom_ingredient_ingredients_category',
                    'category_id',
                    'custom_ingredient_category_entity',
                    'entity_id'
                ),
                'category_id',
                $installer->getTable('custom_ingredient_category_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    'custom_ingredient_ingredients_category',
                    'ingredient_id',
                    'custom_ingredient_entity',
                    'entity_id'
                ),
                'ingredient_id',
                $installer->getTable('custom_ingredient_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )->setComment('Ingredient To Category Linkage Table');
        $installer->getConnection()->createTable($table);

        /** @var \Custom\Ingredients\Setup\EavTablesSetup $eavTablesSetup */
        $eavTablesSetup = $this->eavTablesSetupFactory->create(['setup' => $installer]);
        $eavTablesSetup->createEavTables(IngredientSetup::ENTITY_TYPE_CODE);
    }
    /**
     * @param SchemaSetupInterface $installer
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function installCategoryEntity(SchemaSetupInterface $installer)
    {
        $tableName = CategorySetup::ENTITY_TYPE_CODE . '_entity';
        $table = $installer->getConnection()
            ->newTable($installer->getTable($tableName))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )->setComment('Entity Table');
        $table->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Creation Time'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Update Time'
        );
        $installer->getConnection()->createTable($table);

        /** @var \Custom\Ingredients\Setup\EavTablesSetup $eavTablesSetup */
        $eavTablesSetup = $this->eavTablesSetupFactory->create(['setup' => $installer]);
        $eavTablesSetup->createEavTables(CategorySetup::ENTITY_TYPE_CODE);
    }
}
