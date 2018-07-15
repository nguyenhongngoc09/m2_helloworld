<?php
/**
 * Created by PhpStorm.
 * User: ngocnh
 * Date: 14/07/2018
 * Time: 17:03
 */

namespace SmartOSC\Helloworld\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $conn = $setup->getConnection();

        $tableName = $conn->getTableName('smartosc_staff');

        if (!$conn->isTableExists($tableName)) {
            $table = $conn->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    '',
                    [
                        'indentity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true
                    ]
                )->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false,
                        'default' => ''
                    ]
                )->addColumn(
                    'email',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false,
                        'default' => ''
                    ]
                )->setOption('charset', 'utf8');

            $conn->createTable($table);
        }
        $setup->endSetup();
    }
}