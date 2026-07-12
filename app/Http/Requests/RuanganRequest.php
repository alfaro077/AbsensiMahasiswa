<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RuanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('ruangan');

        return [
            'gedung_id' => 'required|integer|exists:gedung,id',
            'nama'      => 'required|string|max:100',
            'lantai'    => 'required|string|max:50',
            'kapasitas' => 'nullable|integer|min:1',
        ];
    }
}
