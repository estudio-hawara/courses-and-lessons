<?php

namespace CoursesAndLessons;

use CoursesAndLessons\Fields\LessonModule;
use CoursesAndLessons\Fields\LessonOrder;
use CoursesAndLessons\Fields\ModuleCourse;
use CoursesAndLessons\Fields\ModuleOrder;
use CoursesAndLessons\Interfaces\HasActions;
use CoursesAndLessons\Interfaces\HasFilters;
use CoursesAndLessons\Interfaces\HasHooks;
use CoursesAndLessons\Interfaces\HasPath;
use CoursesAndLessons\PostTypes\Lesson;
use CoursesAndLessons\Taxonomies\Course;
use CoursesAndLessons\Taxonomies\Module;

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
    protected ModuleOrder $moduleOrder;

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
        $this->moduleOrder = new ModuleOrder($this);

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
        $this->moduleOrder->addActions();

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