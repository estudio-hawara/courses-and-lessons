<?php

class Course
{
    /**
     * Get all lessons for a course, ordered by module_order and lesson_order
     * 
     * @param int $courseId The course ID
     * @param bool $useCache Whether to use caching (default: true)
     * @return array Array of lesson objects with module information
     */
    public function getLessons(int $courseId, $useCache = true)
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
                l.post_content as lesson_content,
                l.post_excerpt as lesson_excerpt,
                l.post_status as lesson_status,
                l.post_date as lesson_date,
                mt.term_id as lesson_module_id,
                lo.meta_value as lesson_order,
                mt.term_id as module_id,
                mt.name as module_title,
                mtt.description as module_description,
                mt.slug as module_slug,
                mo.meta_value as module_order
            FROM {$wpdb->posts} l
            INNER JOIN {$wpdb->term_relationships} ltr ON l.ID = ltr.object_id
            INNER JOIN {$wpdb->term_taxonomy} ltt ON ltr.term_taxonomy_id = ltt.term_taxonomy_id AND ltt.taxonomy = 'module'
            INNER JOIN {$wpdb->terms} mt ON ltt.term_id = mt.term_id
            INNER JOIN {$wpdb->term_taxonomy} mtt ON mt.term_id = mtt.term_id AND mtt.taxonomy = 'module'
            INNER JOIN {$wpdb->termmeta} mc ON mt.term_id = mc.term_id AND mc.meta_key = 'module_course'
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
        
        if (!$results) {
            wp_cache_set($cacheKey, [], 'courses_and_lessons', 300);

            return [];
        }
        
        $lessons = [];
        foreach ($results as $row) {
            $lessons[] = [
                'lesson' => [
                    'ID' => (int) $row->lesson_id,
                    'title' => $row->lesson_title,
                    'content' => $row->lesson_content,
                    'excerpt' => $row->lesson_excerpt,
                    'status' => $row->lesson_status,
                    'date' => $row->lesson_date,
                    'order' => (int) $row->lesson_order ?: 0,
                    'module_id' => (int) $row->lesson_module_id
                ],
                'module' => [
                    'ID' => (int) $row->module_id,
                    'title' => $row->module_title,
                    'description' => $row->module_description,
                    'slug' => $row->module_slug,
                    'order' => (int) $row->module_order ?: 0
                ]
            ];
        }

        wp_cache_set($cacheKey, $lessons, 'courses_and_lessons', 3600);

        return $lessons;
    }
}