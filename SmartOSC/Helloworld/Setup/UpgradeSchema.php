<?php
/**
 * Created by PhpStorm.
 * User: ngocnh
 * Date: 14/07/2018
 * Time: 17:59
 */

namespace SmartOSC\Helloworld\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * Upgrades data for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
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
        } else {
            $setup->run("ALTER TABLE ". $tableName . " ADD COLUMN status BOOLEAN");
        }
        $setup->endSetup();
    }
}