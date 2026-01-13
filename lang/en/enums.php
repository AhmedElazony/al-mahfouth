<?php

use App\Domains\Tahfidh\Enums\AttendanceStatusesEnum;
use App\Domains\Tahfidh\Enums\DaysEnum;
use App\Domains\Tahfidh\Enums\EducationalStagesEnum;
use App\Domains\Tahfidh\Enums\GradesEnum;
use App\Domains\Tahfidh\Enums\LearningStatusesEnum;
use App\Domains\Tahfidh\Enums\MemorizingAmountsEnum;
use App\Domains\User\Enums\UserGendersEnum;
use App\Domains\User\Enums\UserRolesEnum;

return [
    // UserRolesEnum
    UserRolesEnum::SUPER_ADMIN->value => 'Super Admin',
    UserRolesEnum::ADMIN->value => 'Admin',
    UserRolesEnum::STUDENT->value => 'Student',
    UserRolesEnum::TEACHER->value => 'Teacher',

    // UserGendersEnum
    UserGendersEnum::MALE->value => 'Male',
    UserGendersEnum::FEMALE->value => 'Female',

    // AttendanceStatusesEnum
    AttendanceStatusesEnum::ATTENDED->value => 'Attended',
    AttendanceStatusesEnum::ABSENT->value => 'Absent',
    AttendanceStatusesEnum::EXCUSED->value => 'Excused',

    // EducationLevelsEnum
    EducationalStagesEnum::NO_SCHOOL->value => 'No School',
    EducationalStagesEnum::PRIMARY->value => 'Primary Stage',
    EducationalStagesEnum::PREPARATORY->value => 'Preparatory Stage',
    EducationalStagesEnum::SECONDARY->value => 'Secondary Stage',
    EducationalStagesEnum::UNIVERSITY->value => 'University Stage',
    EducationalStagesEnum::GRADUATE->value => 'Graduate',

    // GradesEnum
    GradesEnum::BAD->value => 'Bad',
    GradesEnum::GOOD->value => 'Good',
    GradesEnum::VERY_GOOD->value => 'Very Good',
    GradesEnum::EXCELLENT->value => 'Excellent',

    // LearningStatusesEnum
    LearningStatusesEnum::NOT_STARTED->value => 'Not Started',
    LearningStatusesEnum::IN_PROGRESS->value => 'In Progress',
    LearningStatusesEnum::COMPLETED->value => 'Completed',

    // MemorizingAmountsEnum
    MemorizingAmountsEnum::LESS_THAN_ONE_QUARTER->value => 'Less than one quarter',
    MemorizingAmountsEnum::ONE_QUARTER->value => 'One quarter',
    MemorizingAmountsEnum::HALF_HIZB->value => 'Half Hizb',
    MemorizingAmountsEnum::ONE_HIZB->value => 'One Hizb',
    MemorizingAmountsEnum::ONE_JUZ->value => 'One Juz',
    MemorizingAmountsEnum::MORE_THAN_ONE_JUZ->value => 'More than one Juz',

    // DaysEnum
    DaysEnum::SATURDAY->value => 'Saturday',
    DaysEnum::SUNDAY->value => 'Sunday',
    DaysEnum::MONDAY->value => 'Monday',
    DaysEnum::TUESDAY->value => 'Tuesday',
    DaysEnum::WEDNESDAY->value => 'Wednesday',
    DaysEnum::THURSDAY->value => 'Thursday',
    DaysEnum::FRIDAY->value => 'Friday',
];
