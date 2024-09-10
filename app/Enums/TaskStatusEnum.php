<?php
namespace App\Enums;

enum TaskStatusEnum: string
{
    case TODO = 'TODO';
    case IN_PROGRESS = 'IN_PROGRESS';
    case READY_FOR_TEST = 'READY_FOR_TEST';
    case PO_REVIEW = 'PO_REVIEW';
    case DONE = 'DONE';
    case REJECTED = 'REJECTED';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
