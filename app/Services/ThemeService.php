<?php

namespace App\Services;

use App\Models\Setting;

class ThemeService
{
    private const THEMES = [
        'purple' => ['#7C3AED', '#5B21B6', '#EDE9FE'],
        'green'  => ['#10B981', '#059669', '#D1FAE5'],
        'blue'   => ['#3B82F6', '#1D4ED8', '#DBEAFE'],
        'orange' => ['#F97316', '#C2410C', '#FFEDD5'],
    ];

    public const THEME_NAMES = [
        'purple' => 'Roxo',
        'green'  => 'Verde Neon',
        'blue'   => 'Azul Elétrico',
        'orange' => 'Laranja',
    ];

    /** Returns [primary, primaryDark, primaryLight] for the active theme. */
    public static function colors(): array
    {
        $slug = Setting::get('theme_color', 'purple');
        return self::THEMES[$slug] ?? self::THEMES['purple'];
    }

    public static function activeSlug(): string
    {
        return Setting::get('theme_color', 'purple');
    }

    public static function cssVars(): string
    {
        [$p, $d, $l] = self::colors();

        return implode("\n", [
            ":root {",
            "  --color-primary:       {$p};",
            "  --color-primary-dark:  {$d};",
            "  --color-primary-light: {$l};",
            "}",
        ]);
    }

    public static function allThemes(): array
    {
        $themes = [];
        foreach (self::THEMES as $slug => [$p, $d, $l]) {
            $themes[] = [
                'slug'    => $slug,
                'name'    => self::THEME_NAMES[$slug],
                'primary' => $p,
                'dark'    => $d,
                'light'   => $l,
            ];
        }
        return $themes;
    }
}
