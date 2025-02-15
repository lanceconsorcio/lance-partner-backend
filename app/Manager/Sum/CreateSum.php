<?php

namespace App\Manager\Sum;

use App\Jobs\UpdateSellerCommissionsJob;
use App\Manager\DataTransferObject\Sum\QuotaDTO;
use App\Manager\DataTransferObject\Sales\SaleDTO;
use App\Manager\DataTransferObject\Sum\SumDTO;
use App\Manager\Sales\Commissions\CreateCompanyCommissions;
use App\Models\Branchs;
use App\Models\Card;
use App\Models\Companies;
use App\Models\CompanyCommissions;
use App\Models\Contracts;
use App\Models\Customers;
use App\Models\Leads;
use App\Models\Quotas;
use App\Models\Sum;
use App\Models\User;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateSum {

    private User $partner;
    private Sum $sum;

    public function __construct(private SumDTO $sumDTO) {}

    /**
     * @throws Exception|Throwable
     */
    public function execute() {
        DB::beginTransaction();

        try {
            $this->createSum();
            //$this->createQuotas();

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    private function createSum() {
        if($sum = Sum::create($this->sumDTO->fill())){
            $this->sum = $sum;

            $this->createQuotas();

            return response()->json($sum);
        }
    }

    private function createQuotas() {
        // $quota = new Quotas();
        // $quota->fill($this->saleDTO->quota->fill() + [
        //     'contract_id' => $this->contract->id,
        //     'customer_id' => $this->customer->id,
        //     'company_id' => $this->saleDTO->companyId,
        //     'branch_id' => $this->saleDTO->branchId,
        //     'seller_id' => $this->saleDTO->sellerId
        // ]);
        // $quota->save();

        // $this->quota = $quota;
        //$quotas = $this->sumDTO->quotas;

        array_map(function (QuotaDTO $quota) {
            Card::create(array_merge($quota->fill(), ["sum_id" => $this->sum->id]));
        }, $this->sumDTO->quotas);
    }

}
