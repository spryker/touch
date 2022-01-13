<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Touch\Business;

use Orm\Zed\Touch\Persistence\Map\SpyTouchTableMap;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Spryker\Zed\Touch\Business\TouchBusinessFactory getFactory()
 * @method \Spryker\Zed\Touch\Persistence\TouchEntityManagerInterface getEntityManager()
 */
class TouchFacade extends AbstractFacade implements TouchFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $itemType
     * @param int $idItem
     * @param bool $keyChange
     *
     * @return bool
     */
    public function touchActive($itemType, $idItem, $keyChange = false)
    {
        $touchRecordModel = $this->getFactory()->createTouchRecordModel();

        return $touchRecordModel->saveTouchRecord(
            $itemType,
            SpyTouchTableMap::COL_ITEM_EVENT_ACTIVE,
            $idItem,
            $keyChange,
        );
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $itemType
     * @param int $idItem
     *
     * @return bool
     */
    public function touchInactive($itemType, $idItem)
    {
        $touchRecordModel = $this->getFactory()->createTouchRecordModel();

        return $touchRecordModel->saveTouchRecord(
            $itemType,
            SpyTouchTableMap::COL_ITEM_EVENT_INACTIVE,
            $idItem,
        );
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $itemType
     * @param int $idItem
     *
     * @return bool
     */
    public function touchDeleted($itemType, $idItem)
    {
        $touchRecordModel = $this->getFactory()->createTouchRecordModel();

        return $touchRecordModel->saveTouchRecord(
            $itemType,
            SpyTouchTableMap::COL_ITEM_EVENT_DELETED,
            $idItem,
        );
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $itemType
     * @param array $itemIds
     *
     * @return int
     */
    public function bulkTouchSetActive($itemType, array $itemIds)
    {
        $touchModel = $this->getFactory()->createBulkTouchModel();

        return $touchModel->bulkTouch($itemType, SpyTouchTableMap::COL_ITEM_EVENT_ACTIVE, $itemIds);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $itemType
     * @param array $itemIds
     *
     * @return int
     */
    public function bulkTouchSetInActive($itemType, array $itemIds)
    {
        $touchModel = $this->getFactory()->createBulkTouchModel();

        return $touchModel->bulkTouch($itemType, SpyTouchTableMap::COL_ITEM_EVENT_INACTIVE, $itemIds);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $itemType
     * @param array $itemIds
     *
     * @return int
     */
    public function bulkTouchSetDeleted($itemType, array $itemIds)
    {
        $touchModel = $this->getFactory()->createBulkTouchModel();

        return $touchModel->bulkTouch($itemType, SpyTouchTableMap::COL_ITEM_EVENT_DELETED, $itemIds);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $itemType
     *
     * @return array<\Generated\Shared\Transfer\TouchTransfer>
     */
    public function getItemsByType($itemType)
    {
        $touchModel = $this->getFactory()->createTouchModel();

        return $touchModel->getItemsByType($itemType);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @deprecated Use {@link cleanTouchEntitiesForDeletedItemEvent()} instead.
     *
     * @return int
     */
    public function removeTouchEntriesMarkedAsDeleted()
    {
        $touchRecordModel = $this->getFactory()->createTouchRecordModel();

        return $touchRecordModel->removeTouchEntriesMarkedAsDeleted();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return int
     */
    public function cleanTouchEntitiesForDeletedItemEvent(): int
    {
        return $this->getFactory()
            ->createTouchWriter()
            ->cleanTouchEntitiesForDeletedItemEvent();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return bool
     */
    public function isTouchEnabled(): bool
    {
        return $this->getFactory()->getConfig()->isTouchEnabled();
    }
}
