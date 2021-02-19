<?php

declare(strict_types=1);

namespace Osm\Data\Files;

use Osm\Core\App;
use Osm\Core\Attributes\Runs;
use Osm\Core\Module as BaseModule;
use Osm\Data\Files\Instructions\Instruction;
use Osm\Data\Files\Rules\Rule;

/**
 * @property string[] $rule_class_names
 */
class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Framework\Translations\Module::class,
    ];

    /**
     * @param string $text
     * @return Rule[]
     */
    #[Runs(Parser::class)]
    public function parse(string $text): array {
        return Parser::new(['text' => $text])->parse();
    }

    /**
     * @param string $filename
     * @return Rule[]
     */
    public function parseFile(string $filename): array {
        if (!is_file($filename)) {
            return [];
        }

        return $this->parse(file_get_contents($filename));
    }

    /**
     * @param string $path
     * @return \Generator|Instruction[]
     * @noinspection PhpDocSignatureInspection
     */
    #[Runs(DirectoryIterator::class)]
    public function iterate(string $path, array $rules = [],
        string $relativePath = ''): \Generator
    {
        $iterator = DirectoryIterator::new(['path' => $path]);
        $rules = array_merge($rules, $iterator->collectParentRules(
            str_replace('\\', '/', $relativePath)));

        return $iterator->iterate($rules, $relativePath);
    }

    /** @noinspection PhpUnused */
    protected function get_rule_class_names(): array {
        global $osm_app; /* @var App $osm_app */

        $classNames = [];

        foreach ($osm_app->classes as $class) {
            if (!is_a($class->name, Rule::class, true)) {
                continue;
            }

            $className = $class->name;
            if (!($ruleName = $className::$name ?? null)) {
                continue;
            }

            $classNames[$ruleName] = $className;
        }

        return $classNames;
    }
}