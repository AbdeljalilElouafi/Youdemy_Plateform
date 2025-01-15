<?php

namespace App\Model;


class CourseFactory {
    public static function createCourse($type) {
        switch ($type) {
            case 'video':
                return new \App\Model\VideoCourse();
            case 'text':
                return new \App\Model\TextCourse();
            default:
                throw new \Exception("Invalid course type: {$type}");
        }
    }
}