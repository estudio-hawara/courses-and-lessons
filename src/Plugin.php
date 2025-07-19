<?php

require_once 'Interfaces/HasActions.php';
require_once 'Interfaces/HasFilters.php';
require_once 'Interfaces/HasHooks.php';
require_once 'Interfaces/HasPath.php';

require_once 'Templates.php';
require_once 'TextDomain.php';

require_once 'PostTypes/Lesson.php';
require_once 'Taxonomies/Course.php';
require_once 'Taxonomies/Module.php';

require_once 'Fields/LessonModule.php';
require_once 'Fields/LessonOrder.php';
require_once 'Fields/ModuleCourse.php';

class Plugin implements HasActions, HasFilters, HasHooks, HasPath
{
    // Plugin internals
    protected readonly string $file;
    protected readonly string $path;
    protected readonly string $url;
    protected Templates $templates;

    // Post types and taxonomies
    protected Lesson $lesson;
    protected Course $course;
    protected Module $module;

    // Fields
    protected LessonModule $lessonModule;
    protected LessonOrder $lessonOrder;
    protected ModuleCourse $moduleCourse;

    // Translations
    protected TextDomain $textDomain;

    public function __construct(string $file)
    {
        // Plugin internals
        $this->file = $file;
        $this->path = plugin_dir_path($file);
        $this->url = plugin_dir_url($file);
        $this->templates = new Templates($this);

        // Post types and taxonomies
        $this->lesson = new Lesson;
        $this->course = new Course;
        $this->module = new Module;

        // Fields
        $this->lessonOrder = new LessonOrder($this);
        $this->lessonModule = new LessonModule($this);
        $this->moduleCourse = new ModuleCourse($this);

        // Translations
        $this->textDomain = new TextDomain($this);
    }

    public function addActions(): void
    {
        // Post types and taxonomies
        $this->lesson->addActions();
        $this->course->addActions();
        $this->module->addActions();

        // Fields
        $this->lessonOrder->addActions();
        $this->lessonModule->addActions();
        $this->moduleCourse->addActions();

        // Translations
        $this->textDomain->addActions();
    }

    public function addFilters(): void
    {
        // Post types and taxonomies
        $this->lesson->addFilters();

        // Fields
        $this->lessonOrder->addFilters();
    }

    public function registerHooks(): void
    {
        // Plugin internals
        register_activation_hook($this->getFile(), fn()  => flush_rewrite_rules());
        register_deactivation_hook($this->getFile(), fn() => flush_rewrite_rules());
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getTemplates(): Templates
    {
        return $this->templates;
    }
}