<?php

namespace App\Rules;

use App\Models\Bills;
use Illuminate\Contracts\Validation\Rule;

class IsValidSegment implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return ($value === 'Imóvel' || $value === 'Veículo' || $value === 'Máquina Agrícola' || $value === 'Investimento' || $value === 'Náutico' || $value === 'Energia Solar');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O segmento precisa ser exatamente igual a: "Imóvel", "Veículo", "Máquina Agrícola", "Investimento", "Náutico" ou "Energia Solar".';
    }
}
