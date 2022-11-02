<?php
/**
 * Stenders_Ingredients
 *
 * @category Stenders
 * @package  Stenders_Ingredients
 * @author   Hussein Noureddine <hussein.noureddine@scandiweb.com>
 */
declare(strict_types=1);
namespace Custom\Ingredients\Model\Attribute\Backend;

class Image extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    private $imageUploader;

    public function __construct(\Custom\Ingredients\Model\ImageUploader $imageUploader)
    {
        $this->imageUploader = $imageUploader;
    }
 
    private function getUploadedImageName($value)
    {
        if (is_array($value) && isset($value[0]['name'])) {
            return $value[0]['name'];
        }

        return '';
    }

    public function beforeSave($object)
    {
        parent::beforeSave($object);
        $attributeName = $this->getAttribute()->getName();
        $value = $object->getData($attributeName);
        $oldFilename = $object->getOrigData($attributeName);
        $newFilename = $this->getUploadedImageName($value);

        if (!$oldFilename || $newFilename != $oldFilename) {
            if ($newFilename) {
                $object->setData($attributeName, $this->imageUploader->moveFileFromTmp($newFilename));
            } elseif (!is_string($value)) {
                $object->setData($attributeName, null);
            }
        } else {
            if ($oldFilename) {
                $object->setData($attributeName, $oldFilename);
            } elseif (!is_string($value)) {
                $object->setData($attributeName, null);
            }
        }

        return $this;
    }

    public function beforeDelete($object) 
    {
        $attributeValue = $this->getImageValues($object);
        if ($attributeValue) {
            $this->imageUploader->delete($attributeValue);
        }

        return parent::beforeDelete($object);
    }

    private function getImageValues($object)
    {
        $attributeValue = null;
        $attributes = $object->getData();
        $imageAttributeName = $this->getAttribute()->getName();
        foreach ($attributes as $attribute => $attributeValue) {
            if ($attribute === $imageAttributeName) {
                return $attributeValue;
            }
        }
        return $attributeValue;
    }
}
