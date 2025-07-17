<?php

class Plugin
{
    protected Templates $templates;
    protected Lesson $lesson;
    protected Course $course;
    protected CourseDropdown $courseDropdown;
    protected LessonOrder $lessonOrder;
    protected TextDomain $textDomain;

    public function __construct(
        protected readonly string $path,
    ) {
        $this->templates = new Templates($this);
        $this->lesson = new Lesson;
        $this->course = new Course;
        $this->lessonOrder = new LessonOrder($this);
        $this->courseDropdown = new CourseDropdown($this);
        $this->textDomain = new TextDomain($this);
    }

    public function addActions(): void
    {
        $this->lesson->addActions();
        $this->course->addActions();
        $this->lessonOrder->addActions();
        $this->courseDropdown->addActions();
        $this->textDomain->addActions();
    }

    public function addFilters(): void
    {
        $this->lesson->addFilters();
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getTemplates(): Templates
    {
        return $this->templates;
    }
}