<?php

declare(strict_types=1);

namespace Primo\Cli\Console;

use Symfony\Component\Console\Formatter\OutputFormatter;

use function preg_replace;
use function preg_split;

/**
 * Copy/pasted from:
 * @link https://github.com/symplify/console-color-diff/blob/main/src/Console/Formatter/ColorConsoleDiffFormatter.php
 */
final class ConsoleColourDiffFormatter
{
    private const PLUS_START_REGEX = '#^(\+.*)#';
    private const MINUS_START_REGEX = '#^(\-.*)#';
    private const AT_START_REGEX = '#^(@.*)#';
    private const NEWLINES_REGEX = "#\n\r|\n#";

    /** @var string */
    private $template;

    public function __construct()
    {
        $this->template = <<<TEXT
            <comment>    ---------- begin diff ----------</comment>
            %s
            <comment>    ----------- end diff -----------</comment>
            
            TEXT;
    }

    public function format(string $diff): string
    {
        return $this->formatWithTemplate($diff, $this->template);
    }

    private function formatWithTemplate(string $diff, string $template): string
    {
        $escapedDiff = OutputFormatter::escape(rtrim($diff));

        $escapedDiffLines = preg_split(self::NEWLINES_REGEX, $escapedDiff);

        // remove description of added + remove; obvious on diffs
        foreach ($escapedDiffLines as $key => $escapedDiffLine) {
            if ($escapedDiffLine === '--- Original') {
                unset($escapedDiffLines[$key]);
            }
            if ($escapedDiffLine === '+++ New') {
                unset($escapedDiffLines[$key]);
            }
        }

        $coloredLines = array_map(function (string $string): string {
            $string = $this->makePlusLinesGreen($string);
            $string = $this->makeMinusLinesRed($string);
            $string = $this->makeAtNoteCyan($string);

            if ($string === ' ') {
                return '';
            }

            return $string;
        }, $escapedDiffLines);

        return sprintf($template, implode(PHP_EOL, $coloredLines));
    }

    private function makePlusLinesGreen(string $string): string
    {
        return (string) preg_replace(
            self::PLUS_START_REGEX,
            '<fg=green>$1</fg=green>',
            $string
        );
    }

    private function makeMinusLinesRed(string $string): string
    {
        return (string) preg_replace(
            self::MINUS_START_REGEX,
            '<fg=red>$1</fg=red>',
            $string
        );
    }

    private function makeAtNoteCyan(string $string): string
    {
        return (string) preg_replace(
            self::AT_START_REGEX,
            '<fg=cyan>$1</fg=cyan>',
            $string
        );
    }
}
