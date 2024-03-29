<?php

declare(strict_types=1);

namespace Primo\Cli\Type;

use JsonSerializable;

use function sprintf;

final class Spec implements JsonSerializable
{
    private string $filename;

    private function __construct(
        private string $id,
        private string $name,
        private bool $repeatable,
    ) {
        $this->filename = sprintf('%s.json', $this->id);
    }

    public static function new(
        string $id,
        string $name,
        bool $repeatable,
    ): self {
        return new static($id, $name, $repeatable);
    }

    /** @return mixed[] */
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
