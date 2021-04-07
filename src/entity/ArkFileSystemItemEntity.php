<?php


namespace sinri\ark\core\entity;


use sinri\ark\core\ArkHelper;
use sinri\ark\core\exception\NotADirectoryException;
use sinri\ark\core\exception\NotAValidPathException;

/**
 * Class ArkFileSystemItemEntity
 * @package sinri\ark\core\entity
 * @since 2.5
 */
class ArkFileSystemItemEntity
{
    const TYPE_FILE = "FILE";
    const TYPE_DIR = "DIR";

    protected $parentDirectoryPath;
    protected $itemType;
    protected $itemFullName;
    protected $itemName;
    protected $itemExtension;

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getParentDirectoryPath()
    {
        return $this->parentDirectoryPath;
    }

    /**
     * @return mixed
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * @return mixed
     */
    public function getItemFullName()
    {
        return $this->itemFullName;
    }

    /**
     * @return mixed
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * @return mixed
     */
    public function getItemExtension()
    {
        return $this->itemExtension;
    }

    /**
     * @return ArkFileSystemItemEntity[]
     * @throws NotADirectoryException
     * @throws NotAValidPathException
     */
    public function getChildren()
    {
        if ($this->itemType !== self::TYPE_DIR) {
            throw new NotADirectoryException("Not a dir");
        }
        $list = [];
        $handle = opendir($this->getFullPath());
        while (($item = readdir($handle)) !== false) {
            if ($item === '.' || $item === '..') continue;
            $list[] = self::buildWithFullPath($this->getFullPath() . DIRECTORY_SEPARATOR . $item);
        }
        closedir($handle);
        return $list;
    }

    /**
     * @param string $fullPath
     * @return ArkFileSystemItemEntity
     * @throws NotAValidPathException
     */
    public static function buildWithFullPath($fullPath)
    {
        $fullPath = realpath($fullPath);
        if (!$fullPath) {
            throw new NotAValidPathException("It seems not a correct and existing path");
        }

        $entity = new ArkFileSystemItemEntity();
        $pathInfo = pathinfo($fullPath);

        if (is_file($fullPath)) {
            $entity->itemType = self::TYPE_FILE;
            $entity->itemName = $pathInfo['filename'];
            $entity->parentDirectoryPath = $pathInfo['dirname'];
            $entity->itemExtension = ArkHelper::readTarget($pathInfo, ['extension'], "");
            if (strlen($entity->itemExtension) > 0) $entity->itemExtension = '.' . $entity->itemExtension;
            $entity->itemFullName = $pathInfo['basename'];
        } else {
            $entity->itemType = self::TYPE_DIR;
            $entity->itemName = $pathInfo['filename'];
            $entity->parentDirectoryPath = $pathInfo['dirname'];
            $entity->itemExtension = "";
            $entity->itemFullName = $pathInfo['basename'];
        }
        return $entity;
    }

    /**
     * @return false|int
     */
    public function getFileSize()
    {
        return filesize($this->getFullPath());
    }

    public function getFullPath()
    {
        return $this->parentDirectoryPath . DIRECTORY_SEPARATOR . $this->itemFullName;
    }

    public function fetchContentAsAllLines()
    {
        return file($this->getFullPath());
    }

    public function fetchContentAsString()
    {
        return file_get_contents($this->getFullPath());
    }

    public function rewriteFileContent($data)
    {
        return file_put_contents($this->getFullPath(), $data);
    }

    public function appendToFileContent($data)
    {
        return file_put_contents($this->getFullPath(), $data, FILE_APPEND);
    }

}