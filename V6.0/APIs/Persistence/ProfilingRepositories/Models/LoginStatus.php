<?php

namespace API_ProfilingRepositories_Model;

enum LoginStatus
{
    case LOGIN;
    case LOGOUT;
    case LOGIN_FAILED;
}
