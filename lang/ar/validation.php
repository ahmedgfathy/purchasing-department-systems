<?php

return [
    'accepted' => 'يجب قبول :attribute.',
    'array' => 'يجب أن يكون :attribute مصفوفة.',
    'boolean' => 'يجب أن تكون قيمة :attribute صحيحة أو خاطئة.',
    'confirmed' => 'تأكيد :attribute غير مطابق.',
    'current_password' => 'كلمة المرور الحالية غير صحيحة.',
    'date' => 'يجب أن يكون :attribute تاريخًا صالحًا.',
    'email' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صالحًا.',
    'exists' => 'قيمة :attribute المحددة غير صالحة.',
    'image' => 'يجب أن يكون :attribute صورة.',
    'integer' => 'يجب أن يكون :attribute عددًا صحيحًا.',
    'in' => 'قيمة :attribute المحددة غير صالحة.',
    'lowercase' => 'يجب أن يكون :attribute بأحرف صغيرة.',
    'mimes' => 'يجب أن يكون :attribute ملفًا من نوع: :values.',
    'numeric' => 'يجب أن يكون :attribute رقمًا.',
    'required' => 'حقل :attribute مطلوب.',
    'string' => 'يجب أن يكون :attribute نصًا.',
    'unique' => 'قيمة :attribute مستخدمة بالفعل.',

    'alpha_dash' => 'يمكن أن يحتوي :attribute على أحرف وأرقام وشرطات وشرطات سفلية فقط.',

    'max' => [
        'array' => 'يجب ألا يحتوي :attribute على أكثر من :max عنصر.',
        'file' => 'يجب ألا يزيد حجم :attribute عن :max كيلوبايت.',
        'numeric' => 'يجب ألا تزيد قيمة :attribute عن :max.',
        'string' => 'يجب ألا يزيد :attribute عن :max حرفًا.',
    ],

    'min' => [
        'array' => 'يجب أن يحتوي :attribute على :min عنصر على الأقل.',
        'file' => 'يجب ألا يقل حجم :attribute عن :min كيلوبايت.',
        'numeric' => 'يجب ألا تقل قيمة :attribute عن :min.',
        'string' => 'يجب ألا يقل :attribute عن :min أحرف.',
    ],

    'attributes' => [
        'name' => 'الاسم',
        'employee_id' => 'الرقم الوظيفي',
        'email' => 'البريد الإلكتروني',
        'phone' => 'الهاتف',
        'job_title' => 'المسمى الوظيفي',
        'avatar' => 'صورة الملف الشخصي',
        'password' => 'كلمة المرور',
        'current_password' => 'كلمة المرور الحالية',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'roles' => 'الأدوار',
        'roles.*' => 'الدور',
        'permissions' => 'الصلاحيات',
        'permissions.*' => 'الصلاحية',
        'slug' => 'المعرّف',
        'description' => 'الوصف',
    ],
];
