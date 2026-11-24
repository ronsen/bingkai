<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\UpdateImageRequest as BaseUpdateImageRequest;

class UpdateImageRequest extends BaseUpdateImageRequest
{
    /**
     * The public API accepts updates without authentication.
     */
    public function authorize(): bool
    {
        return true;
    }
}
