<?php

namespace App\Enum;

enum SubscriptionStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REVOKED = 'revoked';
}