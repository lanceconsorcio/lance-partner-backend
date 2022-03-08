<?php

namespace App\Http\Requests;

use App\Rules\BranchExists;
use App\Rules\CompanyExists;
use App\Rules\RoleExists;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'slug' => 'nullable|min:3|unique:users',
            'name' => 'nullable|min:3|unique:users',
            'name_display' => 'nullable|min:3',
            'cnpj'=> ['nullable', 'unique:users','regex:/([0-9]{2}[\.]?[0-9]{3}[\.]?[0-9]{3}[\/]?[0-9]{4}[-]?[0-9]{2})|([0-9]{3}[\.]?[0-9]{3}[\.]?[0-9]{3}[-]?[0-9]{2})/'],
            'email' => 'nullable|unique:users|email',
            'email_display' => 'nullable|min:3',
            'address' => 'nullable|min:3',
            'phone' => ['nullable', 'unique:users', 'regex:/^\(?[1-9]{2}\)? ?(?:[2-8]|9[1-9])[0-9]{3}\-?[0-9]{4}$/'],
            'password' => ['nullable','min:8','regex:/^(?=.*[A-Z])(?=.*[!@#$&*])(?=.*[0-9].*[0-9])(?=.*[a-z].*[a-z].*[a-z])/'],

            'logo' => 'file|max:2048|mimetypes:image/jpeg,image/png,',
        ];
    }

    public function messages(){
        return [
            'email.email' => 'Por favor, informe um endereço de e-mail válido',
            'password.min' => 'A senha precisa conter no mínimo 8 caracteres.',
            'password.regex' => 'Ops, tente informar uma senha mais forte.',
            'cpf.regex' => 'Por favor, informe um CPF válido.',
            'phone.regex' => 'Por favor, informe um telefone válido.',
            'role.integer' => 'Tipo de dado inválido para o cargo do usuário.',
        ];
    }
}
