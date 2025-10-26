<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return false;
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'category_id'    => 'required|exists:categories,id',
            'stock_quantity' => 'nullable|integer|min:0',
            'is_active'      => 'sometimes|boolean',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048|dimensions:max_width=2000,max_height=2000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'price.required' => 'Please set a product price.',
            'price.numeric' => 'Price must be a number.',
            'category_id.required' => 'Choose a category for the product.',
            'category_id.exists' => 'Selected category does not exist.',
            'image.image' => 'Uploaded file must be an image.',
            'image.max' => 'Image size must be <= 2 MB.',
            'image.dimensions' => 'Image dimensions must not exceed 2000×2000.',
        ];
    }
}
