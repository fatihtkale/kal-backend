<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Token;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public function company() {
        return $this->belongsTo('App\Models\Company');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getUserByToken($headerToken) {
        try {
            if ($headerToken) {
                $auth_header = explode(' ', $headerToken);
                $token = $auth_header[1];
                $token_parts = explode('.', $token);
                $token_header = $token_parts[1];
                $token_header_json = base64_decode($token_header);
                $token_header_array = json_decode($token_header_json, true);
                $token_id = $token_header_array['jti'];

                return Token::find($token_id)->user;
            } else {
                return false;
            }
        } catch(\Exception $e) {
            return false;
        }
    }
}
