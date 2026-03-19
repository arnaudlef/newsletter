<?php

namespace App\Enum;

enum NewsletterStatus: string 
{
    case PENDING = 'pending';
    case PUBLISHED = 'published';
    case UNPUBLISHED = 'unpublished';
}