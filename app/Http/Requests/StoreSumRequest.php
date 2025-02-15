<?php

namespace App\Http\Requests;

use App\Rules\BillCategoryExists;
use App\Rules\BranchExists;
use App\Rules\CompanyExists;
use App\Rules\IsValidSegment;
use App\Rules\SourceExists;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSumRequest extends FormRequest
{
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'credit'=> ['required','min:1'],
            'entry'=> ['required','min:1'],
            'debt'=> ['required','min:1'],
            'tax'=> ['required','min:1'],
            'deadline'=> ['required','min:1'],
            'installment'=> ['required','min:1'],
            'insurance'=> ['required','min:1'],
            'fund'=> ['required','min:1'],

            'quotas.*.segment' => ['required', new IsValidSegment],
            'quotas.*.adm' => ['required', 'min:1'],
            'quotas.*.cod' => ['required','min:1'],
            'quotas.*.credit' => ['required','min:1'],
            'quotas.*.entry' => ['required','min:1'],
            'quotas.*.debt' => ['required','min:1'],
            'quotas.*.tax' => ['required','min:1'],
            'quotas.*.deadline' => ['required','min:1'],
            'quotas.*.installment' => ['required','min:1'],
            'quotas.*.insurance' => ['required','min:1'],
            'quotas.*.fund' => ['required','min:1'],
        ];
    }

    public function messages(){
        return [
        ];
    }
}
