<?php
namespace Forix\Csp\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context){

        $setup->startSetup();
        $installer = $setup->getConnection();

        $tableName = 'forix_csp_collector';
        $table = $installer->newTable($tableName)
                           ->addColumn('id', Table::TYPE_INTEGER, 20,
                                       ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true])
                           ->addColumn('host', Table::TYPE_TEXT, 255, ['nullable' => false])
                           ->addColumn('directive', Table::TYPE_TEXT, 255, ['nullable' => false])
                           ->addColumn('area', Table::TYPE_TEXT, 255, ['nullable' => true])
                           ->addColumn('is_allowed', Table::TYPE_SMALLINT,
                                       ['unsigned' => true, 'nullable' => false, 'default' => '0'])
                           ->addColumn('raw_data', Table::TYPE_TEXT)
                           ->addColumn('created_at', Table::TYPE_TIMESTAMP);

        $uniqueFields = ['host','directive','area'];
        $indexName = $installer->getIndexName($tableName,$uniqueFields, AdapterInterface::INDEX_TYPE_UNIQUE);
        $table->addIndex($indexName,$uniqueFields);

        $installer->createTable($table);

        $setup->endSetup();
    }
}
