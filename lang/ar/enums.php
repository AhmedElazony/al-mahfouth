<?php

use App\Domains\Tahfidh\Enums\AttendanceStatusesEnum;
use App\Domains\Tahfidh\Enums\EducationalStagesEnum;
use App\Domains\Tahfidh\Enums\GradesEnum;
use App\Domains\Tahfidh\Enums\LearningStatusesEnum;
use App\Domains\Tahfidh\Enums\MemorizingAmountsEnum;
use App\Domains\User\Enums\UserGendersEnum;
use App\Domains\User\Enums\UserRolesEnum;

return [
    // UserRolesEnum
    UserRolesEnum::SUPER_ADMIN->value => 'المشرف العام',
    UserRolesEnum::ADMIN->value => 'مشرف',
    UserRolesEnum::STUDENT->value => 'طالب',
    UserRolesEnum::TEACHER->value => 'معلم',

    // UserGendersEnum
    UserGendersEnum::MALE->value => 'ذكر',
    UserGendersEnum::FEMALE->value => 'أنثى',

    // AttendanceStatusesEnum
    AttendanceStatusesEnum::ATTENDED->value => 'حضر',
    AttendanceStatusesEnum::ABSENT->value => 'غائب',
    AttendanceStatusesEnum::EXCUSED->value => 'غائب بعذر',

    // EducationLevelsEnum
    EducationalStagesEnum::NO_SCHOOL->value => 'بدون تعليم',
    EducationalStagesEnum::PRIMARY->value => 'المرحلة الابتدائية',
    EducationalStagesEnum::PREPARATORY->value => 'المرحلة الإعدادية',
    EducationalStagesEnum::SECONDARY->value => 'المرحلة الثانوية',
    EducationalStagesEnum::UNIVERSITY->value => 'المرحلة الجامعية',
    EducationalStagesEnum::GRADUATE->value => 'خريج',

    // GradesEnum
    GradesEnum::BAD->value => 'ضعيف',
    GradesEnum::GOOD->value => 'جيد',
    GradesEnum::VERY_GOOD->value => 'جيد جدًا',
    GradesEnum::EXCELLENT->value => 'ممتاز',

    // LearningStatusesEnum
    LearningStatusesEnum::NOT_STARTED->value => 'لم يبدأ',
    LearningStatusesEnum::IN_PROGRESS->value => 'قيد التعلم',
    LearningStatusesEnum::COMPLETED->value => 'مكتمل',

    // MemorizingAmountsEnum
    MemorizingAmountsEnum::LESS_THAN_ONE_QUARTER->value => 'أقل من ربع حزب',
    MemorizingAmountsEnum::ONE_QUARTER->value => 'ربع حزب',
    MemorizingAmountsEnum::HALF_HIZB->value => 'نصف حزب',
    MemorizingAmountsEnum::ONE_HIZB->value => 'حزب واحد',
    MemorizingAmountsEnum::ONE_JUZ->value => 'جزء واحد',
    MemorizingAmountsEnum::MORE_THAN_ONE_JUZ->value => 'أكثر من جزء واحد',
];
