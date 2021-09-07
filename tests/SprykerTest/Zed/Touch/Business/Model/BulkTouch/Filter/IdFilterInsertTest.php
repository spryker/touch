<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Touch\Business\Model\BulkTouch\Filter;

use Codeception\Test\Unit;
use Spryker\Zed\Touch\Business\Model\BulkTouch\Filter\IdFilterInsert;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Touch
 * @group Business
 * @group Model
 * @group BulkTouch
 * @group Filter
 * @group IdFilterInsertTest
 * Add your own group annotations below this line
 */
class IdFilterInsertTest extends Unit
{
    /**
     * @var string
     */
    public const ITEM_EVENT_ACTIVE = 'active';

    /**
     * @var \Spryker\Zed\Touch\Business\Model\BulkTouch\Filter\IdFilterInsert|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $idFilterInsert;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->idFilterInsert = $this->getMockBuilder(IdFilterInsert::class)
            ->setMethods(['getIdCollection'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return void
     */
    public function testFilter(): void
    {
        $ids = range(1, 200);

        $this->idFilterInsert->expects($this->once())
            ->method('getIdCollection')
            ->willReturn($ids);

        $result = $this->idFilterInsert->filter($ids, 'foo');

        $this->assertSame([], $result);
    }

    /**
     * @return void
     */
    public function testFilterChunkedAllInDatabase(): void
    {
        $countAboveChunkSize = 500;

        $this->assertTrue(IdFilterInsert::CHUNK_SIZE < $countAboveChunkSize);
        $ids = range(1, $countAboveChunkSize);
        $itemIdChunks = array_chunk($ids, IdFilterInsert::CHUNK_SIZE);
        $this->idFilterInsert
            ->expects($this->exactly(count($itemIdChunks)))
            ->method('getIdCollection')
            ->willReturnOnConsecutiveCalls(...$itemIdChunks);

        $result = $this->idFilterInsert->filter($ids, 'foo');
        $this->assertSame([], $result);
    }

    /**
     * @return void
     */
    public function testFilterChunkedNoneInDatabase(): void
    {
        $countAboveChunkSize = 500;

        $ids = range(1, $countAboveChunkSize);
        $itemIdChunks = array_chunk($ids, IdFilterInsert::CHUNK_SIZE);
        $this->idFilterInsert
            ->expects($this->exactly(count($itemIdChunks)))
            ->method('getIdCollection')
            ->willReturn([]);

        $result = $this->idFilterInsert->filter($ids, 'foo');
        $this->assertSame($ids, $result);
    }
}
