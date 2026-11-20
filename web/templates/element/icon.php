<?php
/**
 * Ein Strich-Icon als Inline-SVG. Das Font-Awesome-Subset der App enthält
 * nur sechs Glyphen, die Navigation braucht mehr.
 *
 * @var string $name
 * @var string|null $class
 */
$paths = [
    'today' => '<path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>',
    'tasks' => '<rect x="3" y="3" width="18" height="18" rx="4"/><path d="m8 12.5 2.8 2.8L16.5 9"/>',
    'time' => '<circle cx="12" cy="13" r="8"/><path d="M12 9v4l2.5 1.8M10 2.5h4"/>',
    'customers' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.8-3.5 3.4-5.5 6.5-5.5s5.7 2 6.5 5.5M16 4.6a3.5 3.5 0 0 1 0 6.8M18 14.8c1.9.7 3.1 2.5 3.5 5.2"/>',
    'projects' => '<path d="M3 6.5A1.5 1.5 0 0 1 4.5 5H9l2 2.5h8.5A1.5 1.5 0 0 1 21 9v9.5a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 18.5z"/>',
    'services' => '<path d="m12 3 9 5-9 5-9-5z"/><path d="m3 13 9 5 9-5"/>',
    'reports' => '<path d="M5 20v-8M12 20V5M19 20v-5M3 20h18"/>',
    'receivables' => '<path d="M6 3h12v18l-3-2-3 2-3-2-3 2z"/><path d="M9 8h6M9 12h6M9 16h3"/>',
    'statuses' => '<path d="M4 6h16M4 12h11M4 18h6"/>',
    'backups' => '<ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v12c0 1.7 3.6 3 8 3s8-1.3 8-3V6M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>',
    'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
    'plus' => '<path d="M12 5v14M5 12h14"/>',
    'close' => '<path d="M6 6l12 12M18 6 6 18"/>',
];
?>
<svg class="icon <?= h($class ?? '') ?>" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
     stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $paths[$name] ?? '' ?></svg>
