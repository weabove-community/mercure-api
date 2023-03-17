<?php

namespace App\Service\Blockchain\ERC20\WeAbove;

use App\Enum\WeAbove\L3PStatusEnum;

class StatusService
{
    public function isOverflow($grvScore): bool
    {
        return $grvScore > 200;
    }

    public function isCanDoBetter($grvScore): bool
    {
        return $grvScore < 29;
    }

    public function isOptimize($grvScore): bool
    {
        return $grvScore >= 29 && $grvScore <= 30;
    }

    public function isStrategy($grvScore): bool
    {
        return $grvScore > 30 && $grvScore <= 200;
    }

    /**
     * @param int $grvScore
     * @return string
     * @throws \Exception
     */
    public function define(int $grvScore): string
    {
        switch (true) {
            case $this->isCanDoBetter($grvScore):
                return L3PStatusEnum::CAN_DO_BETTER->value;
            case $this->isOptimize($grvScore):
                return L3PStatusEnum::OPTIMIZE->value;
            case $this->isStrategy($grvScore):
                return L3PStatusEnum::STRATEGY->value;
            case $this->isOverflow($grvScore):
                return L3PStatusEnum::OVERFLOW->value;
            default:
                throw new \Exception(sprintf('Unknow status with %s GRV', $grvScore));
        }
    }
}