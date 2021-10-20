<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Touch\Business\Model;

use DateTime;
use Orm\Zed\Touch\Persistence\Map\SpyTouchTableMap;
use Orm\Zed\Touch\Persistence\SpyTouch;
use Orm\Zed\Touch\Persistence\SpyTouchQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Spryker\Service\UtilDataReader\UtilDataReaderServiceInterface;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\Touch\Persistence\TouchQueryContainerInterface;
use Spryker\Zed\Touch\TouchConfig;

class TouchRecord implements TouchRecordInterface
{
    use TransactionTrait;

    /**
     * @var \Spryker\Service\UtilDataReader\UtilDataReaderServiceInterface
     */
    protected $utilDataReaderService;

    /**
     * @var \Spryker\Zed\Touch\Persistence\TouchQueryContainerInterface
     */
    protected $touchQueryContainer;

    /**
     * @var \Propel\Runtime\Connection\ConnectionInterface
     */
    protected $connection;

    /**
     * @var \Spryker\Zed\Touch\TouchConfig
     */
    protected $touchConfig;

    /**
     * @param \Spryker\Service\UtilDataReader\UtilDataReaderServiceInterface $utilDataReaderService
     * @param \Spryker\Zed\Touch\Persistence\TouchQueryContainerInterface $queryContainer
     * @param \Propel\Runtime\Connection\ConnectionInterface $connection
     * @param \Spryker\Zed\Touch\TouchConfig $touchConfig
     */
    public function __construct(
        UtilDataReaderServiceInterface $utilDataReaderService,
        TouchQueryContainerInterface $queryContainer,
        ConnectionInterface $connection,
        TouchConfig $touchConfig
    ) {
        $this->utilDataReaderService = $utilDataReaderService;
        $this->touchQueryContainer = $queryContainer;
        $this->connection = $connection;
        $this->touchConfig = $touchConfig;
    }

    /**
     * @param string $itemType
     * @param string $itemEvent
     * @param int $idItem
     * @param bool $keyChange
     *
     * @return bool
     */
    public function saveTouchRecord(
        $itemType,
        $itemEvent,
        $idItem,
        $keyChange = false
    ) {
        if ($this->touchConfig->isTouchEnabled()) {
            $this->getTransactionHandler()->handleTransaction(function () use ($itemType, $itemEvent, $idItem, $keyChange): void {
                $this->executeSaveTouchRecordTransaction($itemType, $itemEvent, $idItem, $keyChange);
            });
        }

        return true;
    }

    /**
     * @param string $itemType
     * @param string $itemEvent
     * @param int $idItem
     * @param bool $keyChange
     *
     * @return void
     */
    protected function executeSaveTouchRecordTransaction(string $itemType, string $itemEvent, int $idItem, bool $keyChange = false): void
    {
        if ($keyChange) {
            $this->insertKeyChangeRecord($itemType, $idItem);

            if ($itemEvent === SpyTouchTableMap::COL_ITEM_EVENT_DELETED) {
                if (!$this->deleteKeyChangeActiveRecord($itemType, $idItem)) {
                    $this->insertTouchRecord(
                        $itemType,
                        $itemEvent,
                        $idItem,
                        SpyTouchTableMap::COL_ITEM_EVENT_ACTIVE,
                    );
                }
            } else {
                $this->insertTouchRecord($itemType, $itemEvent, $idItem);
            }
        } else {
            $touchEntity = $this->findOrCreateTouchEntity(
                $itemType,
                $idItem,
                $itemEvent,
            );

            $this->saveTouchEntity($itemType, $idItem, $itemEvent, $touchEntity);
        }
    }

    /**
     * @param string $itemType
     * @param int $idItem
     * @param string $itemEvent
     *
     * @return \Orm\Zed\Touch\Persistence\SpyTouch
     */
    protected function findOrCreateTouchEntity(string $itemType, int $idItem, string $itemEvent): SpyTouch
    {
        $touchEntityCollection = $this->touchQueryContainer->queryUpdateTouchEntry(
            $itemType,
            $idItem,
        )->find();

        if ($touchEntityCollection->count() === 1) {
            return $touchEntityCollection->getFirst();
        }

        foreach ($touchEntityCollection as $touch) {
            if ($touch->getItemEvent() === $itemEvent) {
                return $touch;
            }
        }

        return (new SpyTouch())
            ->setItemType($itemType)
            ->setItemId($idItem)
            ->setItemEvent($itemEvent);
    }

    /**
     * @param string $itemType
     * @param int $idItem
     * @param string $itemEvent
     * @param \Orm\Zed\Touch\Persistence\SpyTouch $touchEntity
     *
     * @return void
     */
    protected function saveTouchEntity(
        $itemType,
        $idItem,
        $itemEvent,
        SpyTouch $touchEntity
    ) {
        $touchEntity->setItemType($itemType)
            ->setItemEvent($itemEvent)
            ->setItemId($idItem)
            ->setTouched(new DateTime());
        $touchEntity->save();
    }

    /**
     * @param string $itemType
     * @param int $idItem
     *
     * @return bool
     */
    protected function deleteKeyChangeActiveRecord($itemType, $idItem)
    {
        $touchDeletedEntity = $this->touchQueryContainer
            ->queryUpdateTouchEntry(
                $itemType,
                $idItem,
                SpyTouchTableMap::COL_ITEM_EVENT_DELETED,
            )
            ->findOne();

        if ($touchDeletedEntity === null) {
            return false;
        }

        $touchActiveEntity = $this->touchQueryContainer
            ->queryUpdateTouchEntry(
                $itemType,
                $idItem,
                SpyTouchTableMap::COL_ITEM_EVENT_ACTIVE,
            )
            ->findOne();

        if ($touchActiveEntity !== null) {
            $touchActiveEntity->delete();
        }

        return true;
    }

    /**
     * @param string $itemType
     * @param int $idItem
     *
     * @return void
     */
    protected function insertKeyChangeRecord($itemType, $idItem)
    {
        $touchOldEntity = $this->touchQueryContainer
            ->queryUpdateTouchEntry(
                $itemType,
                $idItem,
                SpyTouchTableMap::COL_ITEM_EVENT_ACTIVE,
            )
            ->findOne();

        if ($touchOldEntity === null) {
            return;
        }

        $touchDeletedEntity = $this->touchQueryContainer
            ->queryUpdateTouchEntry(
                $itemType,
                $idItem,
                SpyTouchTableMap::COL_ITEM_EVENT_DELETED,
            )
            ->findOne();

        if ($touchDeletedEntity === null) {
            $this->saveTouchEntity(
                $itemType,
                $idItem,
                SpyTouchTableMap::COL_ITEM_EVENT_DELETED,
                $touchOldEntity,
            );
        }
    }

    /**
     * @param string $itemType
     * @param string $itemEvent
     * @param int $idItem
     * @param string|null $type
     *
     * @return void
     */
    protected function insertTouchRecord(
        $itemType,
        $itemEvent,
        $idItem,
        $type = null
    ) {
        if ($type === null) {
            $type = $itemEvent;
        }

        $touchEntity = $this->touchQueryContainer->queryUpdateTouchEntry(
            $itemType,
            $idItem,
            $type,
        )->findOneOrCreate();

        $this->saveTouchEntity($itemType, $idItem, $itemEvent, $touchEntity);
    }

    /**
     * Removes all the rows from the touch table(s)
     * which are marked as deleted (SpyTouchTableMap::COL_ITEM_EVENT_DELETED).
     * Returns the number of Touch entries deleted.
     *
     * @api
     *
     * @throws \Throwable
     *
     * @return int
     */
    public function removeTouchEntriesMarkedAsDeleted()
    {
        if (!$this->touchConfig->isTouchEnabled()) {
            return 0;
        }

        return $this->getTransactionHandler()->handleTransaction(function (): int {
            return $this->executeRemoveTouchEntriesMarkedAsDeletedTransaction();
        });
    }

    /**
     * @return int
     */
    protected function executeRemoveTouchEntriesMarkedAsDeletedTransaction(): int
    {
        $touchListQuery = $this->touchQueryContainer
            ->queryTouchListByItemEvent(
                SpyTouchTableMap::COL_ITEM_EVENT_DELETED,
            );

        return $this->removeTouchEntries($touchListQuery);
    }

    /**
     * @param \Orm\Zed\Touch\Persistence\SpyTouchQuery $query
     *
     * @return int
     */
    protected function removeTouchEntries(SpyTouchQuery $query)
    {
        $deletedCount = 0;
        $batchCollection = $this->getTouchIdsToRemoveBatchCollection($query);

        /** @var \Propel\Runtime\Collection\ArrayCollection $batch */
        foreach ($batchCollection as $batch) {
            $touchIdsToRemove = $batch->toArray();
            $this->removeTouchDataForCollectors($touchIdsToRemove);
            $deletedCount += $query
                ->filterByIdTouch($touchIdsToRemove, Criteria::IN)
                ->delete();
        }

        return $deletedCount;
    }

    /**
     * @param \Orm\Zed\Touch\Persistence\SpyTouchQuery $query
     *
     * @return \Spryker\Service\UtilDataReader\Model\BatchIterator\CountableIteratorInterface
     */
    protected function getTouchIdsToRemoveBatchCollection(SpyTouchQuery $query)
    {
        $touchIdsToRemoveQuery = $query->select(SpyTouchTableMap::COL_ID_TOUCH);

        return $this->utilDataReaderService->getPropelBatchIterator($touchIdsToRemoveQuery);
    }

    /**
     * Removes Touch data in any of the database tables for Collectors
     * If a different Collector table is added to the system, this code should
     * be updated or overridden to include covering that table as well
     *
     * @param array $touchIds
     *
     * @return void
     */
    protected function removeTouchDataForCollectors(array $touchIds)
    {
        $this->touchQueryContainer
            ->queryTouchSearchByTouchIds($touchIds)
            ->delete();

        $this->touchQueryContainer
            ->queryTouchStorageByTouchIds($touchIds)
            ->delete();
    }
}
