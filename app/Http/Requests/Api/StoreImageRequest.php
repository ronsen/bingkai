<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\StoreImageRequest as BaseStoreImageRequest;

class StoreImageRequest extends BaseStoreImageRequest
{
    /**
     * The public API accepts uploads without authentication.
     */
    public function authorize(): bool
    {
        return true;
    }
}
