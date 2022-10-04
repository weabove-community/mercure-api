<?php

namespace App\Enum;

enum CollectionStatusEnum: string
{
    case ADDED = 'added';
    case METADATA_IMPORTED = 'metadata-impored';
    case TRAIT_SAVED = 'trait-saved';
    case TOKEN_ATTRIBUTE_SAVED = 'token-attribute-saved';
    case ATTRIBUTE_PERCENT_PROCESSED = 'attribute-percent-processed';
    case RANK_EXECUTED = 'rank-executed';
}
