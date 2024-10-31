<?php

namespace App\Http\Requests\Api\v1;

use App\Enums\Permission\ProductEnum;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can(ProductEnum::EDIT->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('product')->id;

        return [
            'title' => ['string', 'min:2', 'max:255', Rule::unique(Product::class, 'title')->ignore($id)],
            'SKU' => ['string', 'min:1', 'max:35', Rule::unique(Product::class, 'title')->ignore($id)],
            'description' => ['nullable', 'string'],
            'price' => ['numeric', 'min:1'],
            'discount' => ['numeric', 'min:0', 'max:99'],
            'quantity' => ['numeric', 'min:0'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png'],
            'options.*.attribute_option_id' => ['integer', 'exists:attribute_options,id'],
            'options.*.quantity' => ['numeric', 'min:0'],
            'options.*.price' => ['numeric', 'min:1'],
        ];
    }
}
