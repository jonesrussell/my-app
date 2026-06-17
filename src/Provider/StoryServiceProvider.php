<?php

declare(strict_types=1);

namespace App\Provider;

use App\Entity\Story;
use Waaseyaa\Entity\EntityType;
use Waaseyaa\Foundation\ServiceProvider\ServiceProvider;

final class StoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->entityType(EntityType::fromClass(Story::class, group: 'content'));
    }
}
