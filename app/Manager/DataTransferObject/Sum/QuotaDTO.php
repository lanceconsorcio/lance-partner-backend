<?php

namespace App\Manager\DataTransferObject\Sum;

use App\Manager\DataTransferObject\AbstractDTO;
use App\Manager\DataTransferObject\InterfaceDTO;
use App\Models\Bills;

class QuotaDTO extends AbstractDTO implements InterfaceDTO {

    public float $credit;
    public float $entry;
    public float $debt;
    public float $tax;
    public float $fund;
    public int $cod;
    public int $deadline;
    public int $installment;
    public int $insurance;
    public string $adm;
    public string $segment;

    public static function transform(array $data): self {
        $quota = new self();
        
        $quota->credit = $data['credit'];
        $quota->segment = $data['segment'];
        $quota->cod = $data['cod'];
        $quota->adm = $data['adm'];
        $quota->entry = $data['entry'];
        $quota->debt = $data['debt'];
        $quota->tax = $data['tax'];
        $quota->fund = $data['fund'];
        $quota->deadline = $data['deadline'];
        $quota->installment = $data['installment'];
        $quota->insurance = $data['insurance'];

        return $quota;
    }

}
