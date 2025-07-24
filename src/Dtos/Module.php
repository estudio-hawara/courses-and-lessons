<?php

namespace CoursesAndLessons\Dtos;

class Module
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $description,
        public readonly string $slug,
        public readonly int $order,
    ) {}
}