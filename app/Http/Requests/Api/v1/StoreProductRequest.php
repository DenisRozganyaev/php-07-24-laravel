<?php

namespace App\Http\Requests\Api\v1;

use App\Enums\Permission\ProductEnum;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can(ProductEnum::PUBLISH->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:2', 'max:255', 'unique:products,title'],
            'SKU' => ['required', 'string', 'min:1', 'max:35', 'unique:products,SKU'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:1'],
            'discount' => ['required', 'numeric', 'min:0', 'max:99'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'categories.*' => ['required', 'integer', 'exists:categories,id'],
            'thumbnail' => ['required', 'image', 'mimes:jpg,jpeg,png'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png']
        ];
    }
}
