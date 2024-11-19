<?php

namespace App\Traits;

use App\Models\Project;
use Illuminate\Support\Str;

trait GeneratesTokenTrait
{
    public function GenerateToken()
    {
        $token = Str::random(32);
        if (Project::where('invite_link_token', $token)->first()) {
            return $this->generateToken();
        }
        return $token;
    }
}
