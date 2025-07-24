<?php

namespace CoursesAndLessons\Dtos;

class Lesson
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $name,
        public readonly int $module_id,
        public readonly int $order,
    ) {}
}