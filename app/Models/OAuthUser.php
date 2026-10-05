<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\HasApiTokens;

/**
 * User authenticated by Passport OAuth tokens for the MCP server
 *
 * Separate from User because the Sanctum and Passport HasApiTokens traits
 * can't be used in the same class hierarchy.
 */
class OAuthUser extends Authenticatable implements OAuthenticatable
{
    use HasApiTokens;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
