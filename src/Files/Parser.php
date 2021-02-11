<?php

/** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

namespace Osm\Data\Files;

use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Data\Files\Exceptions\ParsingError;
use Osm\Data\Files\Rules\Rule;
use Osm\Core\Attributes\Expected;
use function Osm\__;

/**
 * @property string $text #[Expected]
 * @property ?string $filename #[Expected]
 * @property Module $module
 */
class Parser extends Object_
{
    const COMMENT_REGEX = '/(^|\n)\s*#[^\n]*/';
    const RULE_REGEX = '/(?<pattern>[^\s]+)\s*(?<rule>[^\s]+)(?<json>\s*(?<has_json>{)(?:\s*(?<has_data>.+))?)?/';

    /**
     * @return Rule[]
     */
    public function parse(): array {
        // strip out comments
        $text = preg_replace(static::COMMENT_REGEX, "\n",
            $this->text);
        $lines = explode("\n", $text);

        $rules = [];
        $json = null;

        foreach ($lines as $lineNo => $line) {
            if (!trim($line)) {
                continue;
            }

            if ($json === null) {
                if (!preg_match(static::RULE_REGEX, $line, $match)) {
                    throw new ParsingError(__(":filename(:line_no): Invalid rule syntax", [
                        'filename' => $this->filename ?? '',
                        'line_no' => $lineNo,
                    ]));
                }

                $className = $this->module->rule_class_names[$match['rule']] ?? null;
                if (!$className) {
                    throw new ParsingError(__(":filename(:line_no): Unknown rule ':rule'", [
                        'filename' => $this->filename ?? '',
                        'line_no' => $lineNo,
                        'rule' => $match['rule'],
                    ]));
                }

                $new = "$className::new";
                $rules[] = $rule = $new(['pattern' => $match['pattern']]);

                if (!isset($match['has_json'])) {
                    continue;
                }

                if (isset($match['has_data'])) {
                    $rule->data = json_decode($match['json']);
                    continue;
                }

                $json = $match['json'];
            }
            else {
                $json .= "\n{$line}";
                if (trim($line) == '}') {
                    /** @noinspection PhpUndefinedVariableInspection */
                    $rule->data = json_decode($json);
                    $json = null;
                }
            }
        }

        if ($json !== null) {
            throw new ParsingError(__(":filename(:line_no): Ending rule data '}' expected", [
                'filename' => $this->filename ?? '',
                'line_no' => count($lines),
            ]));
        }

        return $rules;
    }

    /** @noinspection PhpUnused */
    protected function get_module(): Module {
        global $osm_app; /* @var App $osm_app */

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $osm_app->modules[Module::class];
    }
}