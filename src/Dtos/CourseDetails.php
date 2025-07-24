<?php

namespace CoursesAndLessons\Dtos;

class CourseDetails
{
    protected array $modulesOrder = [];

    protected array $lessonsOrder = [];

    protected array $modules = [];

    protected array $lessons = [];

    public function __construct(
        protected readonly int $id,
        protected readonly string $name,
        protected readonly string $slug,
    ) {}

    public function addLesson(Lesson $lesson, Module $module)
    {
        if (! in_array($module->id, $this->modulesOrder))
            $this->modulesOrder[] = $module->id;

        if (! in_array($lesson->id, $this->lessonsOrder))
            $this->lessonsOrder[] = $lesson->id;

        $this->modules[$module->id] = $module;
        $this->lessons[$lesson->id] = $lesson;
    }

    public function asArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'modules_order' => $this->modulesOrder,
            'lessons_order' => $this->lessonsOrder,
            'modules' => array_map(fn($module) => get_object_vars($module), $this->modules),
            'lessons' => array_map(fn($lesson) => get_object_vars($lesson), $this->lessons),
        ];
    }
}