<?php

namespace App\Enum;

enum SubscriptionStatus: string
{
    case PENDING = 'pending';
    case REFUSED = 'refused';
    case ACCEPTED = 'accepted';
    case REVOKED = 'revoked';
}