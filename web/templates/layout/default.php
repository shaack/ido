<?php
/**
 * Das App-Gerüst: Seitenleiste mit gruppierter Navigation, das Blatt mit dem
 * Inhalt, auf schmalen Bildschirmen statt der Seitenleiste eine Tab-Leiste
 * unten. Die Seitenleiste ist dort ein Offcanvas hinter "Menu".
 *
 * Die Zahlen in der Navigation (offene Tasks, heute erfasste Stunden) setzt
 * AppController::beforeRender().
 *
 * @var \App\View\AppView $this
 */

$controller = $this->request->getParam('controller');
$action = $this->request->getParam('action');
$navOpenTasks = $this->get('navOpenTasks');
$navTodayHours = (float)$this->get('navTodayHours', 0);

// Ein Eintrag gilt als aktiv, wenn Controller und, falls angegeben, die
// Action passen. Offene Forderungen ist ein Report, steht aber eigenständig
// in der Navigation, deshalb nimmt "Reports" diese Action aus.
$isActive = function (array $item) use ($controller, $action): bool {
    if ($item['controller'] !== $controller) {
        return false;
    }
    if (isset($item['action'])) {
        return $item['action'] === $action;
    }

    return !in_array($action, $item['notActions'] ?? [], true);
};

$navigation = [
    '' => [
        ['label' => __('Today'), 'icon' => 'today', 'url' => '/', 'controller' => 'Dashboard'],
        ['label' => __('Tasks'), 'icon' => 'tasks', 'url' => '/tasks', 'controller' => 'Tasks', 'badge' => $navOpenTasks],
        ['label' => __('Time'), 'icon' => 'time', 'url' => '/time-trackings', 'controller' => 'TimeTrackings'],
    ],
    __('Clients') => [
        ['label' => __('Customers'), 'icon' => 'customers', 'url' => '/customers?current=1', 'controller' => 'Customers'],
        ['label' => __('Projects'), 'icon' => 'projects', 'url' => '/projects?current=1', 'controller' => 'Projects'],
        ['label' => __('Services'), 'icon' => 'services', 'url' => '/services', 'controller' => 'Services'],
    ],
    __('Insights') => [
        ['label' => __('Reports'), 'icon' => 'reports', 'url' => '/reports', 'controller' => 'Reports', 'notActions' => ['receivables']],
        ['label' => __('Receivables'), 'icon' => 'receivables', 'url' => '/reports/receivables', 'controller' => 'Reports', 'action' => 'receivables'],
    ],
    __('Settings') => [
        ['label' => __('Project statuses'), 'icon' => 'statuses', 'url' => '/project-statuses', 'controller' => 'ProjectStatuses'],
        ['label' => __('Backups'), 'icon' => 'backups', 'url' => '/backups', 'controller' => 'Backups'],
    ],
];

// Die Tab-Leiste auf dem Handy: das Tägliche, der Rest steckt im Menü.
$tabs = [$navigation[''][0], $navigation[''][1], $navigation[''][2], $navigation[__('Clients')][1]];
?>
<!DOCTYPE html>
<html lang="de" data-bs-theme="auto">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#EDF0F3" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#15191F" media="(prefers-color-scheme: dark)">
    <title><?= $this->fetch('title') ?> · Ido</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <?= $this->Html->meta('icon') ?>
    <link rel="preload" href="/assets/fonts/public-sans-latin-wght-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/assets/fonts/bricolage-grotesque-latin-wght-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link href="/assets/fontawesome-subset/css/fontawesome.min.css" rel="stylesheet">
    <link href="/assets/fontawesome-subset/css/solid.min.css" rel="stylesheet">
    <link href="/assets/fontawesome-subset/css/regular.min.css" rel="stylesheet">

    <?php // Änderungsdatum als Version, damit nach einem Deploy kein alter Stand im Cache hängt ?>
    <link href="/assets/styles/screen.css?v=<?= @filemtime(WWW_ROOT . 'assets/styles/screen.css') ?>" rel="stylesheet">

    <script type="importmap">
        {
            "imports": {
                "cm-web-modules/": "/node_modules/cm-web-modules/",
                "cm-md-editor/": "/node_modules/cm-md-editor/"
            }
        }
    </script>

    <script src="/node_modules/bootstrap-auto-dark-mode/src/bootstrap-auto-dark-mode.js"></script>
    <script src="/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/node_modules/bootstrap-show-toast/src/bootstrap-show-toast.js"></script>
    <script src="/assets/scripts/task-toggle.js?v=<?= @filemtime(WWW_ROOT . 'assets/scripts/task-toggle.js') ?>" defer></script>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body class="app<?= $this->get('hideNavigation') ? ' app-bare' : '' ?>">
<?php if (!$this->get('hideNavigation')) : ?>
    <aside class="app-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="app-nav" aria-label="<?= __('Navigation') ?>">
        <div class="app-sidebar-head">
            <?= $this->element('logo') ?>
            <button type="button" class="btn app-icon-btn d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#app-nav"
                    aria-label="<?= __('Close menu') ?>"><?= $this->element('icon', ['name' => 'close']) ?></button>
        </div>
        <nav class="app-nav">
            <?php foreach ($navigation as $group => $items) : ?>
                <div class="app-nav-group">
                    <?php if ($group !== '') : ?>
                        <div class="app-nav-label"><?= h($group) ?></div>
                    <?php endif; ?>
                    <?php foreach ($items as $item) : $active = $isActive($item) ?>
                        <a class="app-nav-item<?= $active ? ' active' : '' ?>" href="<?= h($item['url']) ?>"
                            <?= $active ? 'aria-current="page"' : '' ?>>
                            <?= $this->element('icon', ['name' => $item['icon']]) ?>
                            <span><?= h($item['label']) ?></span>
                            <?php if (!empty($item['badge'])) : ?>
                                <span class="app-nav-badge"><?= (int)$item['badge'] ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </nav>
        <a class="app-sidebar-today" href="/">
            <span class="app-sidebar-today-label"><?= __('Today') ?></span>
            <span class="app-sidebar-today-hours"><?= $this->Effort->hours($navTodayHours) ?> h</span>
            <?= $this->element('ruler', ['hours' => $navTodayHours, 'small' => true]) ?>
        </a>
    </aside>
<?php endif; ?>

<div class="app-main">
    <?php if (!$this->get('hideNavigation')) : ?>
        <header class="app-mobilebar d-lg-none">
            <?= $this->element('logo') ?>
            <span class="app-mobilebar-today"><?= $this->Effort->hours($navTodayHours) ?> h <?= __('today') ?></span>
        </header>
    <?php endif; ?>
    <main class="app-sheet main">
        <?php
        /** @noinspection PhpUndefinedVariableInspection */
        if (@$timeTracking && $timeTracking->task) {
            $task = $timeTracking->task;
            $service = $task->service;
            $project = $service->project;
            $customer = $project->customer;
        } else if (@$task && $task->service) {
            $service = $task->service;
            $project = $service->project;
            $customer = $project->customer;
        } else if (@$service && $service->project) {
            $project = $service->project;
            $customer = $project->customer;
        } else if (@$project && $project->customer) {
            $customer = $project->customer;
        }
        // Auf den Anlege-Formularen liegt eine leere Entity vor. Die ist
        // truthy, hat aber weder Name noch Id, und CakePHP 5 lässt kein
        // null mehr als Linktext zu. Deshalb wird auf die Id geprüft.
        $crumbs = [];
        if (@$customer && $customer->id) {
            $crumbs[] = $this->Html->link((string)$customer->shortcut, ['controller' => 'Customers', 'action' => 'view', $customer->id]);
        }
        if (@$project && $project->id) {
            $crumbs[] = $this->Html->link((string)$project->name, ['controller' => 'Projects', 'action' => 'view', $project->id]);
        }
        if (@$service && $service->id) {
            $crumbs[] = $this->Html->link($service->name ?: "[Service]", ['controller' => 'Services', 'action' => 'view', $service->id]);
        }
        if (@$task && $task->id) {
            $crumbs[] = $this->Html->link($task->name ?: "[Task]", ['controller' => 'Tasks', 'action' => 'view', $task->id]);
        }
        if (@$timeTracking && $timeTracking->id) {
            $crumbs[] = $this->Html->link("[Tracking]", ['controller' => 'TimeTrackings', 'action' => 'view', $timeTracking->id]);
        }
        ?>
        <?php if ($crumbs) : ?>
            <nav class="app-breadcrumb" aria-label="<?= __('Breadcrumb') ?>">
                <ol class="breadcrumb">
                    <?php foreach ($crumbs as $crumb) : ?>
                        <li class="breadcrumb-item"><?= $crumb ?></li>
                    <?php endforeach; ?>
                </ol>
            </nav>
        <?php endif; ?>
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
    </main>
</div>

<?php if (!$this->get('hideNavigation')) : ?>
    <nav class="app-tabbar d-lg-none" aria-label="<?= __('Quick navigation') ?>">
        <?php foreach ($tabs as $item) : $active = $isActive($item) ?>
            <a class="app-tab<?= $active ? ' active' : '' ?>" href="<?= h($item['url']) ?>" <?= $active ? 'aria-current="page"' : '' ?>>
                <?= $this->element('icon', ['name' => $item['icon']]) ?>
                <span><?= h($item['label']) ?></span>
                <?php if (!empty($item['badge'])) : ?>
                    <span class="app-nav-badge"><?= (int)$item['badge'] ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
        <button type="button" class="app-tab" data-bs-toggle="offcanvas" data-bs-target="#app-nav" aria-controls="app-nav">
            <?= $this->element('icon', ['name' => 'menu']) ?>
            <span><?= __('Menu') ?></span>
        </button>
    </nav>
<?php endif; ?>

<script type="module">
    import {MdEditor} from "cm-md-editor/src/MdEditor.js";

    let editors = document.querySelectorAll("textarea.markdown")
    for (const editor of editors) {
        editor.classList.add("font-monospace")
        editor.style.tabSize = 4
        new MdEditor(editor)
    }
</script>
</body>
</html>
