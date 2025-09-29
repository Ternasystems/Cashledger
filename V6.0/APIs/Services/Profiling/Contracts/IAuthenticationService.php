<?php

namespace API_Profiling_Contract;

use API_ProfilingEntities_Model\Credential;

interface IAuthenticationService
{
    public function Authenticate(string $username, string $password, string $ip): ?Credential;
    public function Deauthenticate(string $sessionId): bool;
    public function SessionPayload(string $sessionId): ?array;
}