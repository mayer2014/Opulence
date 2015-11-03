<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
/**
 * Defines the Redis-cached SQL data mapper
 */
namespace Opulence\Orm\DataMappers;

use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\Redis\Redis;

abstract class RedisCachedSqlDataMapper extends CachedSqlDataMapper
{
    /**
     * @param Redis $cache The cache object used in the cache data mapper
     * @param ConnectionPool $connectionPool The connection pool used in the SQL data mapper
     */
    public function __construct(Redis $cache, ConnectionPool $connectionPool)
    {
        parent::__construct($cache, $connectionPool);
    }
} 