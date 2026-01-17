<?php

namespace App\Enums;

enum PlanType: string // <--- Ini yang dimaksud 'Backed enum (String)'
{
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';
}
