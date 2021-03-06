<?php

namespace App\Models;

use Core\Model;

/**
 * User Model
 * 
 * @package App\Models
 * @uses Core\Model Core Model
 * @author Mohammed-Aymen Benadra
 */
class User extends Model
{
    public function __construct()
    {
        parent::__construct([
            'id' => 'required|int|exists:User',
            'avatar' => 'string', // Auto generated from Gravatar
            'username' => 'required|string|unique:User,username',
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
            'is_admin' => 'bool',
        ]);
        $this->table = 'Users';
    }
}
