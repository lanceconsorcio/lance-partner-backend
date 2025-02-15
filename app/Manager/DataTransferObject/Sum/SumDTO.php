<?php

namespace App\Manager\DataTransferObject\Sum;

use App\Manager\DataTransferObject\AbstractDTO;
use App\Manager\DataTransferObject\InterfaceDTO;
use App\Manager\DataTransferObject\Sum\QuotaDTO;

class SumDTO extends AbstractDTO implements InterfaceDTO {

    public int $user_id;
    public float $credit;
    public float $entry;
    public float $debt;
    public float $tax;
    public float $fund;
    public int $deadline;
    public int $installment;
    public int $insurance;

    public array $quotas;

    public static function transform(array $data): self {
        $sale = new self();

        $sale->user_id = $data['user_id'];
        $sale->credit = $data['credit'];
        $sale->entry = $data['entry'];
        $sale->debt = $data['debt'];
        $sale->tax = $data['tax'];
        $sale->fund = $data['fund'];
        $sale->deadline = $data['deadline'];
        $sale->installment = $data['installment'];
        $sale->insurance = $data['insurance'];
        $sale->quotas = isset($data['quotas']) ? array_map(function(array $quota){ return QuotaDTO::transform($quota);}, $data['quotas']) : [];

        return $sale;
    }

}
