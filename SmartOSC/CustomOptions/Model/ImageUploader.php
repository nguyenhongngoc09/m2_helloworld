<?php
/**
 * Created by PhpStorm.
 * User: ngocnh
 * Date: 18/07/2018
 * Time: 16:02
 */

namespace SmartOSC\CustomOptions\Model;


/**
 * Catalog image uploader
 */
class ImageUploader extends \Magento\Catalog\Model\ImageUploader
{
    public function __construct(
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        string $baseTmpPath,
        string $basePath,
        array $allowedExtensions
    ) {
        parent::__construct(
            $coreFileStorageDatabase,
            $filesystem,
            $uploaderFactory,
            $storeManager,
            $logger,
            $baseTmpPath,
            $basePath,
            $allowedExtensions
        );
    }
}