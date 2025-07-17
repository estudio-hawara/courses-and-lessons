<?php

require_once 'Interfaces/HasActions.php';
require_once 'Interfaces/HasFilters.php';
require_once 'Interfaces/HasPath.php';

require_once 'Templates.php';
require_once 'TextDomain.php';

require_once 'PostTypes/Lesson.php';
require_once 'Taxonomies/Course.php';

require_once 'Fields/LessonCourse.php';
require_once 'Fields/LessonOrder.php';

class Plugin implements HasActions, HasFilters, HasPath
{
    protected Templates $templates;
    protected Lesson $lesson;
    protected Course $course;
    protected LessonCourse $lessonCourse;
    protected LessonOrder $lessonOrder;
    protected TextDomain $textDomain;

    public function __construct(
        protected readonly string $path,
        protected readonly string $url,
    ) {
        $this->templates = new Templates($this);
        $this->lesson = new Lesson;
        $this->course = new Course;
        $this->lessonOrder = new LessonOrder($this);
        $this->lessonCourse = new LessonCourse($this);
        $this->textDomain = new TextDomain($this);
    }

    public function addActions(): void
    {
        $this->lesson->addActions();
        $this->course->addActions();
        $this->lessonOrder->addActions();
        $this->lessonCourse->addActions();
        $this->textDomain->addActions();
    }

    public function addFilters(): void
    {
        $this->lesson->addFilters();
        $this->lessonOrder->addFilters();
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