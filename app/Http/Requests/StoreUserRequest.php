<?php

namespace App\Http\Requests;

use App\Rules\CompanyExists;
use App\Rules\RoleExists;
use App\Rules\TeamExists;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
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
            'slug' => 'required|min:3|unique:users',
            'name' => 'required|min:3|unique:users',
            'name_display' => 'nullable|min:3',
            'cnpj'=> ['required', 'unique:users','regex:/([0-9]{2}[\.]?[0-9]{3}[\.]?[0-9]{3}[\/]?[0-9]{4}[-]?[0-9]{2})|([0-9]{3}[\.]?[0-9]{3}[\.]?[0-9]{3}[-]?[0-9]{2})/'],
            'email' => 'required|unique:users|email',
            'email_display' => 'nullable|min:3',
            'address' => 'nullable|min:3',
            'phone' => ['required', 'unique:users', 'regex:/^\(?[1-9]{2}\)? ?(?:[2-8]|9[1-9])[0-9]{3}\-?[0-9]{4}$/'],
            'password' => ['required', 'min:8', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[\W]).*$/'],

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
