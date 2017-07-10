<?php

namespace Ethos\Track404\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    protected $_log;

    /**
     * @param \Ethos\Track404\Helper\Logs $log
     */
    public function __construct(
        \Ethos\Track404\Helper\Logs $log
    )
    {
        $this->_log = $log;
    }


    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->_log->info("[InstallSchema/install] : start");

        $setup->startSetup();
        /**
         * Create table 'ethos_url_noroute'
         */

        if (!$setup->getConnection()->isTableExists($setup->getTable('ethos_url_noroute'))) {
            $table = $setup->getConnection()
                ->newTable($setup->getTable('ethos_url_noroute'))
                ->addColumn(
                    'url',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'primary' => true],
                    'url'

                )
                ->addColumn(
                    'count',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Name'
                )
                ->addColumn(
                    'date_first_time',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'first time url was called'
                )
                ->addColumn(
                    'date_last_time',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'last time url was called'
                )
                ->addColumn(
                    'referer',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'source of the wrong url'
                )
                ->setComment('Ethos url no route Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');

            $setup->getConnection()->createTable($table);
        } else  $this->_log->info("[InstallSchema/install] : Table [ethos_url_noroute] already exists in database");

        $setup->endSetup();
        $this->_log->info("[InstallSchema/install] : finish");

    }
}