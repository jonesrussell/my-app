<?php

declare(strict_types=1);

namespace App\Entity;

use Waaseyaa\Entity\Attribute\ContentEntityKeys;
use Waaseyaa\Entity\Attribute\ContentEntityType;
use Waaseyaa\Entity\Attribute\Field;
use Waaseyaa\Entity\ContentEntityBase;

#[ContentEntityType(id: 'story', label: 'Story')]
#[ContentEntityKeys(label: 'title')]
final class Story extends ContentEntityBase
{
    #[Field(type: 'boolean', label: 'Published', default: true)]
    public bool $status = true;

    #[Field(type: 'string', label: 'Title')]
    public string $title = '';

    #[Field(type: 'text', label: 'Body')]
    public ?string $body = null;

    #[Field(type: 'string', label: 'Source Url')]
    public string $source_url = '';
}
