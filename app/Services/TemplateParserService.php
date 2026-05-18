<?php

namespace App\Services;

class TemplateParserService {
    public function parse(
        string $content,
        array $data
    ): string {

        foreach ($data as $key => $value) {

            $content = str_replace(
                '{{' . $key . '}}',
                $value ?? '',
                $content
            );
        }

        return $content;
    }
}
