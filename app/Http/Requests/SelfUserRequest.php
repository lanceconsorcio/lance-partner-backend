<?php

namespace App\Http\Requests;

use App\Rules\CompanyExists;
use App\Rules\RoleExists;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SelfUserRequest extends FormRequest
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
        $userId = Auth::user()->id;

        return [
            'slug' => ['nullable','min:3', Rule::unique('users')->ignore($userId, 'id')],
            'name' => ['nullable','min:3', Rule::unique('users')->ignore($userId, 'id')],
            'name_display' => 'nullable|min:3',
            'email_display' => 'nullable|min:3',
            'address' => 'nullable|min:3',

            'cnpj'=> ['nullable', Rule::unique('users')->ignore($userId, 'id'), 'regex:/([0-9]{2}[\.]?[0-9]{3}[\.]?[0-9]{3}[\/]?[0-9]{4}[-]?[0-9]{2})|([0-9]{3}[\.]?[0-9]{3}[\.]?[0-9]{3}[-]?[0-9]{2})/'],
            'email' => ['nullable','email', Rule::unique('users')->ignore($userId, 'id')],
            'phone' => ['nullable', 'regex:/^\(?[1-9]{2}\)? ?(?:[2-8]|9[1-9])[0-9]{3}\-?[0-9]{4}$/', Rule::unique('users')->ignore($userId, 'id')],

            'password' => ['nullable', 'min:8','regex:/^(?=.*[A-Z])(?=.*[!@#$&*])(?=.*[0-9].*[0-9])(?=.*[a-z].*[a-z].*[a-z])/'],

            'logo' => 'nullable|file|max:2048|mimetypes:image/jpeg,image/png,',
        ];
    }

    public function messages(){
        return [
            'role.required' => 'Selecione um cargo para o usuário.',
            'company.required' => 'Selecione pelo menos uma empresa para o usuário trabalhar.',

            'email.email' => 'Por favor, informe um endereço de e-mail válido',
            'password.min' => 'A senha precisa conter no mínimo 8 caracteres.',
            'password.regex' => 'Ops, tente informar uma senha mais forte.',
            'cpf.regex' => 'Por favor, informe um CPF válido.',
            'phone.regex' => 'Por favor, informe um telefone válido.',
            'role.integer' => 'Tipo de dado inválido para o cargo do usuário.',
        ];
    }
}
