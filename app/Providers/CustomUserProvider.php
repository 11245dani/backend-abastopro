<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class CustomUserProvider extends EloquentUserProvider
{
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return;
        }

        // Usamos 'correo' en vez de 'email'
        $query = $this->createModel()->newQuery();

        foreach ($credentials as $key => $value) {
            if (!str_contains($key, 'password')) {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }
}
