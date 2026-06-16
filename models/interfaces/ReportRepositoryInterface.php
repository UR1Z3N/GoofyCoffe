<?php
interface ReportRepositoryInterface {
    public function getDailyRecap($date);
    public function getPaymentMethodStats($date);
    public function getMonthlyRecap($month);
    public function getMonthlyPaymentMethodStats($month);
}
?>
