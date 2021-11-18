<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Touch\Persistence;

use DateTime;
use Generated\Shared\Transfer\LocaleTransfer;
use Orm\Zed\Touch\Persistence\SpyTouchQuery;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;

interface TouchQueryContainerInterface extends QueryContainerInterface
{
    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $itemType
     *
     * @return \Orm\Zed\Touch\Persistence\SpyTouchQuery
     */
    public function queryTouchListByItemType($itemType);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $itemType
     * @param string $itemId
     *
     * @return \Orm\Zed\Touch\Persistence\SpyTouchQuery
     */
    public function queryTouchEntry($itemType, $itemId);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $itemType
     * @param int $itemId
     * @param string|null $itemEvent
     *
     * @return \Orm\Zed\Touch\Persistence\SpyTouchQuery
     */
    public function queryUpdateTouchEntry($itemType, $itemId, $itemEvent = null);

    /**
     * Specification:
     *  - return all items with given `$itemType` and `$itemId` whether they are active, inactive or deleted
     *
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $itemType
     * @param array $itemIds
     *
     * @return \Orm\Zed\Touch\Persistence\SpyTouchQuery
     */
    public function queryTouchEntriesByItemTypeAndItemIds($itemType, array $itemIds);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $itemType
     * @param int $idStore
     * @param int|null $idLocale
     *
     * @return \Orm\Zed\Touch\Persistence\SpyTouchQuery
     */
    public function queryTouchDeleteStorageAndSearch($itemType, $idStore, $idLocale = null);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @return \Orm\Zed\Touch\Persistence\SpyTouchQuery
     */
    public function queryExportTypes();

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $itemType
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param \DateTime $lastTouchedAt
     *
     * @return \Orm\Zed\Touch\Persistence\SpyTouchQuery
     */
    public function createBasicExportableQuery($itemType, LocaleTransfer $localeTransfer, DateTime $lastTouchedAt);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $itemEvent
     *
     * @return \Orm\Zed\Touch\Persistence\SpyTouchQuery
     */
    public function queryTouchListByItemEvent($itemEvent);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param array<int>|int $touchIds
     *
     * @return \Orm\Zed\Touch\Persistence\SpyTouchSearchQuery
     */
    public function queryTouchSearchByTouchIds($touchIds);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param array<int>|int $touchIds
     *
     * @return \Orm\Zed\Touch\Persistence\SpyTouchStorageQuery
     */
    public function queryTouchStorageByTouchIds($touchIds);

    /**
     * Specification:
     * - Filters `SpyTouchQuery` by item type, item event and array of item ids and returns query object.
     *
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $itemType
     * @param string $itemEvent
     * @param array $itemIds
     *
     * @return \Orm\Zed\Touch\Persistence\SpyTouchQuery
     */
    public function queryTouchEntriesByItemTypeAndItemEventAndItemIds(string $itemType, string $itemEvent, array $itemIds): SpyTouchQuery;

    /**
     * Specification:
     * - Filters `spy_touch` entries by item type, item ids and with the same item event.
     * - If there are no records with same item event, returns a record if there are only one record with provided item type and item id.
     *
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $itemType
     * @param string $itemEvent
     * @param array $itemIds
     *
     * @return \Orm\Zed\Touch\Persistence\SpyTouchQuery
     */
    public function queryTouchEntriesByItemTypeAndItemIdsAllowableToUpdateWithItemEvent(string $itemType, string $itemEvent, array $itemIds): SpyTouchQuery;

    /**
     * Specification:
     * - Filters `spy_touch` entries by array of touch ids.
     *
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param array $touchIds
     *
     * @return \Orm\Zed\Touch\Persistence\SpyTouchQuery
     */
    public function queryTouchEntriesByTouchIds(array $touchIds): SpyTouchQuery;
}
