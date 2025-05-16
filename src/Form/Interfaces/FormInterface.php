<?php
namespace CryCMS\Form\Interfaces;

use CryCMS\Exceptions\ThingValidateException;
use CryCMS\Interfaces\ThingInterface;

interface FormInterface
{
    /** @noinspection PhpUnused */
    public const array FIELDS = [];

    public static function find(): ThingInterface;

    /** @throws ThingValidateException */
    public function save(): bool;

    public function delete(): bool;

    public function setAttributes(array $values, bool $withDefault = false): void;

    public function getFieldsList(): array;

    public function addError(string $field, string $error): void;

    public function getError(string $field): ?string;
}