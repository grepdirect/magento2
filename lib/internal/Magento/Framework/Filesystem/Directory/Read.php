<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Filesystem\Directory;

use Magento\Framework\Filesystem\FilesystemException;

class Read implements ReadInterface
{
    /**
     * Directory path
     *
     * @var string
     */
    protected $path;

    /**
     * File factory
     *
     * @var \Magento\Framework\Filesystem\File\ReadFactory
     */
    protected $fileFactory;

    /**
     * Filesystem driver
     *
     * @var \Magento\Framework\Filesystem\DriverInterface
     */
    protected $driver;

    /**
     * Constructor. Set properties.
     *
     * @param \Magento\Framework\Filesystem\File\ReadFactory $fileFactory
     * @param \Magento\Framework\Filesystem\DriverInterface $driver
     * @param string $path
     */
    public function __construct(
        \Magento\Framework\Filesystem\File\ReadFactory $fileFactory,
        \Magento\Framework\Filesystem\DriverInterface $driver,
        $path
    ) {
        $this->fileFactory = $fileFactory;
        $this->driver = $driver;
        $this->setPath($path);
    }

    /**
     * Sets base path
     *
     * @param string $path
     * @return void
     */
    protected function setPath($path)
    {
        if (!empty($path)) {
            $this->path = rtrim(str_replace('\\', '/', $path), '/') . '/';
        }
    }

    /**
     * Retrieves absolute path
     * E.g.: /var/www/application/file.txt
     *
     * @param string $path
     * @param string $scheme
     * @return string
     */
    public function getAbsolutePath($path = null, $scheme = null)
    {
        return $this->driver->getAbsolutePath($this->path, $path, $scheme);
    }

    /**
     * Retrieves relative path
     *
     * @param string $path
     * @return string
     */
    public function getRelativePath($path = null)
    {
        return $this->driver->getRelativePath($this->path, $path);
    }

    /**
     * Retrieve list of all entities in given path
     *
     * @param string|null $path
     * @return string[]
     */
    public function read($path = null)
    {
        $files = $this->driver->readDirectory($this->driver->getAbsolutePath($this->path, $path));
        $result = [];
        foreach ($files as $file) {
            $result[] = $this->getRelativePath($file);
        }
        return $result;
    }

    /**
     * Read recursively
     *
     * @param null $path
     * @return string[]
     */
    public function readRecursively($path = null)
    {
        $result = [];
        $paths = $this->driver->readDirectoryRecursively($this->driver->getAbsolutePath($this->path, $path));
        /** @var \FilesystemIterator $file */
        foreach ($paths as $file) {
            $result[] = $this->getRelativePath($file);
        }
        sort($result);
        return $result;
    }

    /**
     * Search all entries for given regex pattern
     *
     * @param string $pattern
     * @param string $path [optional]
     * @return string[]
     */
    public function search($pattern, $path = null)
    {
        if ($path) {
            $absolutePath = $this->driver->getAbsolutePath($this->path, $this->getRelativePath($path));
        } else {
            $absolutePath = $this->path;
        }

        $files = $this->driver->search($pattern, $absolutePath);
        $result = [];
        foreach ($files as $file) {
            $result[] = $this->getRelativePath($file);
        }
        return $result;
    }

    /**
     * Check a file or directory exists
     *
     * @param string $path [optional]
     * @return bool
     * @throws \Magento\Framework\Filesystem\FilesystemException
     */
    public function isExist($path = null)
    {
        return $this->driver->isExists($this->driver->getAbsolutePath($this->path, $path));
    }

    /**
     * Gathers the statistics of the given path
     *
     * @param string $path
     * @return array
     * @throws \Magento\Framework\Filesystem\FilesystemException
     */
    public function stat($path)
    {
        return $this->driver->stat($this->driver->getAbsolutePath($this->path, $path));
    }

    /**
     * Check permissions for reading file or directory
     *
     * @param string $path [optional]
     * @return bool
     * @throws \Magento\Framework\Filesystem\FilesystemException
     */
    public function isReadable($path = null)
    {
        return $this->driver->isReadable($this->driver->getAbsolutePath($this->path, $path));
    }

    /**
     * Open file in read mode
     *
     * @param string $path
     *
     * @return \Magento\Framework\Filesystem\File\ReadInterface
     */
    public function openFile($path)
    {
        return $this->fileFactory->create(
            $this->driver->getAbsolutePath($this->path, $path),
            $this->driver
        );
    }

    /**
     * Retrieve file contents from given path
     *
     * @param string $path
     * @param string|null $flag
     * @param resource|null $context
     * @return string
     * @throws FilesystemException
     */
    public function readFile($path, $flag = null, $context = null)
    {
        $absolutePath = $this->driver->getAbsolutePath($this->path, $path);
        return $this->driver->fileGetContents($absolutePath, $flag, $context);

    }

    /**
     * Check whether given path is file
     *
     * @param string $path
     * @return bool
     */
    public function isFile($path)
    {
        return $this->driver->isFile($this->driver->getAbsolutePath($this->path, $path));
    }

    /**
     * Check whether given path is directory
     *
     * @param string $path [optional]
     * @return bool
     */
    public function isDirectory($path = null)
    {
        return $this->driver->isDirectory($this->driver->getAbsolutePath($this->path, $path));
    }
}
