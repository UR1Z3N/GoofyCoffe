<?php

namespace Tests\Unit;

use Order;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Exception;

/**
 * Unit tests for Order::createOrder using Mock test doubles for PDO/PDOStatement.
 */
class OrderCreateOrderMockTest extends TestCase
{
    private function createItem(int $menuId, int $quantity, int $price, string $notes = ''): array
    {
        return [
            'menu_id' => $menuId,
            'quantity' => $quantity,
            'price' => $price,
            'notes' => $notes,
        ];
    }

    public function testCreateOrderSuccessCommitsTransaction(): void
    {
        $pdo = $this->createMock(PDO::class);
        $stmtOrder = $this->createMock(PDOStatement::class);
        $stmtItems = $this->createMock(PDOStatement::class);

        $pdo->expects($this->once())->method('beginTransaction');
        $pdo->expects($this->once())->method('commit');
        $pdo->expects($this->never())->method('rollBack');

        $pdo->expects($this->exactly(2))
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($stmtOrder, $stmtItems);

        $pdo->expects($this->once())->method('lastInsertId')->willReturn('42');

        $stmtOrder->expects($this->once())->method('execute')->willReturn(true);
        $stmtItems->expects($this->once())->method('execute')->willReturn(true);

        $order = new Order($pdo);
        $result = $order->createOrder(50000, 'Tunai', [
            $this->createItem(1, 2, 25000),
        ]);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('order_no', $result);
        $this->assertStringStartsWith('ORD-', $result['order_no']);
    }

    public function testCreateOrderRollsBackOnExecuteFailure(): void
    {
        $pdo = $this->createMock(PDO::class);
        $stmtOrder = $this->createMock(PDOStatement::class);

        $pdo->expects($this->once())->method('beginTransaction');
        $pdo->expects($this->once())->method('rollBack');
        $pdo->expects($this->never())->method('commit');

        $pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtOrder);

        $stmtOrder->expects($this->once())
            ->method('execute')
            ->willThrowException(new Exception('Simulated database failure'));

        $order = new Order($pdo);
        $result = $order->createOrder(30000, 'QRIS', [
            $this->createItem(2, 1, 30000),
        ]);

        $this->assertFalse($result['success']);
        $this->assertSame('Simulated database failure', $result['message']);
    }

    public function testCreateOrderExecutesForEachCartItem(): void
    {
        $pdo = $this->createMock(PDO::class);
        $stmtOrder = $this->createMock(PDOStatement::class);
        $stmtItems = $this->createMock(PDOStatement::class);

        $pdo->method('beginTransaction');
        $pdo->method('commit');
        $pdo->method('lastInsertId')->willReturn('99');

        $pdo->expects($this->exactly(2))
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($stmtOrder, $stmtItems);

        $stmtOrder->expects($this->once())->method('execute')->willReturn(true);
        $stmtItems->expects($this->exactly(2))->method('execute')->willReturn(true);

        $order = new Order($pdo);
        $result = $order->createOrder(75000, 'Tunai', [
            $this->createItem(1, 1, 25000),
            $this->createItem(3, 2, 25000, 'Less ice'),
        ]);

        $this->assertTrue($result['success']);
        $this->assertStringStartsWith('ORD-', $result['order_no']);
    }
}
