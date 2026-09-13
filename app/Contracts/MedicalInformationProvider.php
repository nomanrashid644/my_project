<?php

namespace App\Contracts;

interface MedicalInformationProvider
{
    public function name(): string;

    /** @return array{message: string, tokens_used: int|null} */
    public function answer(array $messages): array;
}