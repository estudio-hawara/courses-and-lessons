<?php

namespace CoursesAndLessons\Queries;

use CoursesAndLessons\Dtos\CourseDetails;
use CoursesAndLessons\Dtos\Lesson;
use CoursesAndLessons\Dtos\Module;

class Courses
{
    /**
     * Get all lessons for a course, ordered by module_order and lesson_order
     * 
     * @param int $courseId The course ID
     * @param bool $useCache Whether to use caching (default: true)
     * @return array Array of lesson objects with module information
     */
    public function getDetails(int $courseId, $useCache = true)
    {
        $cacheKey = "course_lessons_{$courseId}";

        if ($useCache) {
            $cachedResult = wp_cache_get($cacheKey, 'courses_and_lessons');

            if ($cachedResult !== false) {
                return $cachedResult;
            }
        }
        
        global $wpdb;
        
        $query = $wpdb->prepare("
            SELECT 
                l.ID as lesson_id,
                l.post_title as lesson_title,
                l.post_name as lesson_name,
                mt.term_id as lesson_module_id,
                lo.meta_value as lesson_order,
                mt.term_id as module_id,
                mt.name as module_title,
                mtt.description as module_description,
                mt.slug as module_slug,
                mo.meta_value as module_order,
                ct.term_id as course_id,
                ct.name as course_name,
                ct.slug as course_slug
            FROM {$wpdb->posts} l
            INNER JOIN {$wpdb->term_relationships} ltr ON l.ID = ltr.object_id
            INNER JOIN {$wpdb->term_taxonomy} ltt ON ltr.term_taxonomy_id = ltt.term_taxonomy_id AND ltt.taxonomy = 'module'
            INNER JOIN {$wpdb->terms} mt ON ltt.term_id = mt.term_id
            INNER JOIN {$wpdb->term_taxonomy} mtt ON mt.term_id = mtt.term_id AND mtt.taxonomy = 'module'
            INNER JOIN {$wpdb->termmeta} mc ON mt.term_id = mc.term_id AND mc.meta_key = 'module_course'
            INNER JOIN {$wpdb->terms} ct ON mc.meta_value = ct.term_id
            LEFT JOIN {$wpdb->postmeta} lo ON l.ID = lo.post_id AND lo.meta_key = 'lesson_order'
            LEFT JOIN {$wpdb->termmeta} mo ON mt.term_id = mo.term_id AND mo.meta_key = 'module_order'
            WHERE 
                l.post_type = 'lesson'
                AND l.post_status = 'publish'
                AND mc.meta_value = %d
            ORDER BY 
                CAST(COALESCE(mo.meta_value, '0') AS UNSIGNED) ASC,
                CAST(COALESCE(lo.meta_value, '0') AS UNSIGNED) ASC,
                l.post_date ASC
        ", $courseId);
        
        $results = $wpdb->get_results($query);
        
        if (! $results) {
            wp_cache_set($cacheKey, [], 'courses_and_lessons', 300);

            return [];
        }

        $course = null;

        foreach ($results as $row) {
            if (is_null($course))
                $course = new CourseDetails(
                    $row->course_id,
                    $row->course_name,
                    $row->course_slug,
                );

            $lesson = new Lesson(
                $row->lesson_id,
                $row->lesson_title,
                $row->lesson_name,
                $row->lesson_module_id,
                $row->lesson_order ?: 0,
            );

            $module = new Module(
                $row->module_id,
                $row->module_title,
                $row->module_description,
                $row->module_slug,
                $row->module_order ?: 0,
            );

            $course->addLesson($lesson, $module);
        }

        $details = $course->asArray();
        wp_cache_set($cacheKey, $details, 'courses_and_lessons', 3600);

        return $details;
    }
}