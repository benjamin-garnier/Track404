<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ethos\Track404\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Catalog\Model\ResourceModel\Product\Gallery;
use Magento\Catalog\Model\Product\Attribute\Backend\Media\ImageEntryConverter;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
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
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /**
         * Create table 'ethos_404_stats'
         */

        $this->_log->info("[UpgradeSchema/upgrade] : start, version =". $context->getVersion());
        if (version_compare($context->getVersion(), '0.2.0', '<')) {
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
        }
        $setup->endSetup();
        $this->_log->info("[InstallSchema/install] : finish");

    }
}