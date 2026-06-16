<?php
interface OrderRepositoryInterface {
    public function createOrder($total_amount, $payment_method, $items);
    public function getOrdersHistory($start_date = null, $end_date = null);
    public function getOrderDetails($order_id);
}
?>
