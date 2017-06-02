<?php

namespace Lodestone\Modules;

/**
 * Class XIVDB
 *
 * @package Lodestone\Modules
 */
class XIVDB
{
    use XIVDBApiTrait;
    use XIVDBDataTrait;

    const MAX_LEVEL = 60;
    const HOST = 'https://api.xivdb.com';
    const HOST_SECURE = 'https://secure.xivdb.com';
    const CACHE_DIR = __DIR__.'/xivdb-api-cache';
    const CACHE_CHECK = self::CACHE_DIR .'/cache';

    /** @var HttpRequest */
    private $http;

    /** @var bool $enabled */
    public static $enabled = [
        'items' => false,
        'exp' => true,
        'classjobs' => true,
        'profile' => true,
        'collectables' => true,
        'attributes' => true,
    ];

    /**
     * XIVDB constructor.
     */
    function __construct()
    {
        // convert to object
        self::$enabled = (Object)self::$enabled;

        // initialize http request
        $this->http = new HttpRequest();

        if (!$this->isApiReady()) {
            $this->apiItems();
            $this->apiExpTable();
            $this->apiClassJobs();
            $this->apiGc();
            $this->apiBaseParams();
            $this->apiTowns();
            $this->apiGuardians();
            $this->apiMinions();
            $this->apiMounts();

            file_put_contents(self::CACHE_CHECK, time());
        }
    }

    /**
     * Set options
     *
     * @param $options
     * @return $this
     */
    public function setOptions($options)
    {
        self::$enabled = (Object)$options;
        return $this;
    }

    /**
     * Delete cache status file, this
     * will cause api to redownload
     * all data and overwrite.
     */
    public function clearCache()
    {
        if ($this->isApiReady()) {
            unlink(self::CACHE_CHECK);
        }
    }

    /**
     * Generate hash
     *
     * @param $value
     * @return bool|string
     */
    private function getStorageHash($value, $length = 8)
    {
        // assuming no collisions for 8 characters,
        // we don't have much data
        return substr(md5(strtolower($value)), 0, $length);
    }
}
