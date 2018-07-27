<?php
/**
 * Created by PhpStorm.
 * User: ngocnh
 * Date: 25/07/2018
 * Time: 17:17
 */

namespace SmartOSC\CustomOptions\Api\Data;


interface ProductCustomOptionInterface extends \Magento\Catalog\Api\Data\ProductCustomOptionInterface
{
    /**
     * Product datetime option type.
     */
    const OPTION_TYPE_THUMB_GALLERY = 'thumb_gallery';
    
    /**
     * Product time option type.
     */
    const OPTION_TYPE_THUMB_GALLERY_POPUP = 'thumb_gallery_popup';
    
    /**
     * Product time option type.
     */
    const OPTION_TYPE_THUMB_GALLERY_MULTI_SELECT = 'thumb_gallery_multi_select';
    
    // /**
    //  * Get option thumb color
    //  *
    //  * @return string
    //  */
    // public function getThumbColor();
    //
    // /**
    //  * Set option thumb color
    //  *
    //  * @param string $thumbColor
    //  * @return $this
    //  */
    // public function setThumbColor($thumbColor);
    //
    // /**
    //  * Get option thumb color
    //  *
    //  * @return string
    //  */
    // public function getDisplayMode();
    //
    // /**
    //  * Set option thumb color
    //  *
    //  * @param string $thumbColor
    //  * @return $this
    //  */
    // public function setDisplayMode($displayMode);
    //
    // /**
    //  * Set option thumb color
    //  *
    //  * @param string $thumbColor
    //  * @return $this
    //  */
    // public function setImage($image);
    //
    // public function getImage();
    //
    // public function setIsDeFault($isDefault);
    //
    // public function getIsDefault();
}