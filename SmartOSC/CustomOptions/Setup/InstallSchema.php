<?php
/**
 * Created by PhpStorm.
 * User: ngocnh
 * Date: 18/07/2018
 * Time: 17:03
 */

namespace SmartOSC\CustomOptions\Setup;

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
        
        $tableName = $conn->getTableName('smartosc_custom_options');
        
        if (!$conn->isTableExists($tableName)) {
            $table = $conn->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true, ]
                )->addColumn(
                    'option_id',
                    Table::TYPE_INTEGER,
                    null,
                    [ 'nullable' => false, 'unsigned' => true, ]
                )->addColumn(
                    'type',
                    Table::TYPE_TEXT,
                    50,
                    [ 'nullable' => true],
                    'Option type'
                )->addColumn(
                    'image',
                    Table::TYPE_TEXT,
                    255,
                    [ 'nullable' => true]
                )->addColumn(
                    'thumb_color',
                    Table::TYPE_TEXT,
                    7,
                    [ 'nullable' => true ]
                )->addColumn(
                    'display_mode',
                    Table::TYPE_TEXT,
                    6,
                    [ 'nullable' => false, 'default' => 'image' ]
                )->addColumn(
                    'is_default',
                    Table::TYPE_SMALLINT,
                    6,
                    [ 'nullable' => false, 'default' => '0' ]
                )->addColumn(
                    'sort_order',
                    Table::TYPE_INTEGER,
                    10,
                    [ 'nullable' => false, 'default' => '0', ]
                )->setOption('charset', 'utf8');
            
            $conn->createTable($table);
        }
        $setup->endSetup();
    }
}