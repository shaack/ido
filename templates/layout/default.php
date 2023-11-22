<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

$controller = $this->request->getParam('controller')
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>
    <link href="/assets/fontawesome-subset/css/fontawesome.min.css" rel="stylesheet">
    <link href="/assets/fontawesome-subset/css/solid.min.css" rel="stylesheet">
    <link href="/assets/fontawesome-subset/css/regular.min.css" rel="stylesheet">

    <link href="/assets/styles/screen.css" rel="stylesheet">

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

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
<header>
    <?php if (!$this->get('hideNavigation')) : ?>
        <nav class="navbar navbar-expand-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?= $this->Url->build('/tasks') ?>"><span class="char-1">I</span><span
                        class="char-2">d</span><span class="char-3">o</span></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link <?= $controller != "Customers" ?: "active" ?>"
                               href="/customers?current=1">Customers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $controller != "Projects" ?: "active" ?>" href="/projects?current=1">Projects</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $controller != "Services" ?: "active" ?>"
                               href="/services">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $controller != "Tasks" ?: "active" ?>" href="/tasks">Tasks</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $controller != "TimeTrackings" ?: "active" ?>"
                               href="/time-trackings">Time Trackings</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php else: ?>
        <div class="mb-2"></div>
    <?php endif ?>
    <div class="container-fluid">
        <div class="breadcrumb">
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
            if (@$customer) {
                echo $this->Html->link($customer->shortcut, ['controller' => 'Customers', 'action' => 'view', $customer->id],
                    ["class" => "breadcrumb-item text-decoration-none"]);
            }
            if (@$project) {
                echo $this->Html->link($project->name, ['controller' => 'Projects', 'action' => 'view', $project->id],
                    ["class" => "breadcrumb-item text-decoration-none"]);
            }
            if (@$service) {
                echo $this->Html->link($service->name ?: "[Service]", ['controller' => 'Services', 'action' => 'view', $service->id],
                    ["class" => "breadcrumb-item text-decoration-none"]);
            }
            if (@$task) {
                echo $this->Html->link($task->name ?: "[Task]", ['controller' => 'Tasks', 'action' => 'view', $task->id],
                    ["class" => "breadcrumb-item text-decoration-none"]);
            }
            if (@$timeTracking) {
                echo $this->Html->link($timeTracking->name ?: "[Tracking]", ['controller' => 'TimeTrackings', 'action' => 'view', $timeTracking->id],
                    ["class" => "breadcrumb-item text-decoration-none"]);
            }
            ?>
        </div>
    </div>
</header>
<main class="main">
    <div class="container-fluid">
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
    </div>
</main>
<footer>
</footer>
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
