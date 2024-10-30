<?php

declare(strict_types=1);

namespace Primo\Cli\Type;

use JsonSerializable;

use function sprintf;

final class Spec implements JsonSerializable
{
    private string $filename;

    /** @param non-empty-string $id */
    private function __construct(
        private string $id,
        private string $name,
        private bool $repeatable,
    ) {
        $this->filename = sprintf('%s.json', $this->id);
    }

    /** @param non-empty-string $id */
    public static function new(
        string $id,
        string $name,
        bool $repeatable,
    ): self {
        return new static($id, $name, $repeatable);
    }

    /** @return array{id: non-empty-string, name: string, repeatable: bool, value: string} */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'repeatable' => $this->repeatable,
            'value' => $this->filename,
        ];
    }

    public function source(): string
    {
        return sprintf('%s.php', $this->id);
    }

    /** @return non-empty-string */
    public function id(): string
    {
        return $this->id;
    }

    public function filename(): string
    {
        return $this->filename;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function repeatable(): bool
    {
        return $this->repeatable;
    }
}
