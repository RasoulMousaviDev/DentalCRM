<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute باید پذیرفته شود.',
    'accepted_if' => ':attribute باید در صورتی که :other برابر با :value باشد پذیرفته شود.',
    'active_url' => ':attribute باید یک URL معتبر باشد.',
    'after' => ':attribute باید تاریخی بعد از :date باشد.',
    'after_or_equal' => ':attribute باید تاریخی بعد یا برابر با :date باشد.',
    'alpha' => ':attribute باید فقط شامل حروف باشد.',
    'alpha_dash' => ':attribute باید فقط شامل حروف، اعداد، خط تیره و زیرخط باشد.',
    'alpha_num' => ':attribute باید فقط شامل حروف و اعداد باشد.',
    'array' => ':attribute باید یک آرایه باشد.',
    'ascii' => ':attribute باید فقط شامل کاراکترهای الفبایی و نمادهای تک بایتی باشد.',
    'before' => ':attribute باید تاریخی قبل از :date باشد.',
    'before_or_equal' => ':attribute باید تاریخی قبل یا برابر با :date باشد.',
    'between' => [
        'array' => ':attribute باید بین :min و :max آیتم داشته باشد.',
        'file' => 'حجم :attribute باید بین :min و :max کیلوبایت باشد.',
        'numeric' => ':attribute باید بین :min و :max باشد.',
        'string' => 'طول :attribute باید بین :min و :max کاراکتر باشد.',
    ],
    'boolean' => ':attribute باید true یا false باشد.',
    'can' => ':attribute شامل یک مقدار غیرمجاز است.',
    'confirmed' => 'تأییدیه :attribute مطابقت ندارد.',
    'contains' => ':attribute فاقد یک مقدار الزامی است.',
    'current_password' => 'رمز عبور اشتباه است.',
    'date' => ':attribute باید یک تاریخ معتبر باشد.',
    'date_equals' => ':attribute باید تاریخی برابر با :date باشد.',
    'date_format' => ':attribute باید با فرمت :format مطابقت داشته باشد.',
    'decimal' => ':attribute باید دارای :decimal رقم اعشار باشد.',
    'declined' => ':attribute باید رد شود.',
    'declined_if' => ':attribute باید در صورتی که :other برابر با :value است رد شود.',
    'different' => ':attribute و :other باید متفاوت باشند.',
    'digits' => ':attribute باید :digits رقم باشد.',
    'digits_between' => ':attribute باید بین :min و :max رقم باشد.',
    'dimensions' => ':attribute دارای ابعاد تصویر نامعتبر است.',
    'distinct' => ':attribute دارای مقدار تکراری است.',
    'doesnt_end_with' => ':attribute نباید با یکی از موارد زیر پایان یابد: :values.',
    'doesnt_start_with' => ':attribute نباید با یکی از موارد زیر شروع شود: :values.',
    'email' => ':attribute باید یک آدرس ایمیل معتبر باشد.',
    'ends_with' => ':attribute باید با یکی از موارد زیر به پایان برسد: :values.',
    'enum' => ':attribute انتخاب شده نامعتبر است.',
    'exists' => ':attribute انتخاب شده نامعتبر است.',
    'extensions' => ':attribute باید دارای یکی از پسوندهای زیر باشد: :values.',
    'file' => ':attribute باید یک فایل باشد.',
    'filled' => ':attribute باید دارای مقدار باشد.',
    'gt' => [
        'array' => ':attribute باید بیشتر از :value آیتم داشته باشد.',
        'file' => 'حجم :attribute باید بیشتر از :value کیلوبایت باشد.',
        'numeric' => ':attribute باید بیشتر از :value باشد.',
        'string' => 'طول :attribute باید بیشتر از :value کاراکتر باشد.',
    ],
    'gte' => [
        'array' => ':attribute باید دارای :value آیتم یا بیشتر باشد.',
        'file' => 'حجم :attribute باید بزرگتر یا برابر با :value کیلوبایت باشد.',
        'numeric' => ':attribute باید بزرگتر یا برابر با :value باشد.',
        'string' => 'طول :attribute باید بزرگتر یا برابر با :value کاراکتر باشد.',
    ],

    'hex_color' => ':attribute باید یک رنگ هگزادسیمال معتبر باشد.',
    'image' => ':attribute باید یک تصویر باشد.',
    'in' => ':attribute انتخاب شده نامعتبر است.',
    'in_array' => ':attribute باید در :other وجود داشته باشد.',
    'integer' => ':attribute باید یک عدد صحیح باشد.',
    'ip' => ':attribute باید یک آدرس IP معتبر باشد.',
    'ipv4' => ':attribute باید یک آدرس IPv4 معتبر باشد.',
    'ipv6' => ':attribute باید یک آدرس IPv6 معتبر باشد.',
    'json' => ':attribute باید یک رشته JSON معتبر باشد.',
    'list' => ':attribute باید یک لیست باشد.',
    'lowercase' => ':attribute باید به صورت حروف کوچک باشد.',

    'lt' => [
        'array' => ':attribute باید کمتر از :value آیتم داشته باشد.',
        'file' => 'حجم :attribute باید کمتر از :value کیلوبایت باشد.',
        'numeric' => ':attribute باید کمتر از :value باشد.',
        'string' => 'طول :attribute باید کمتر از :value کاراکتر باشد.',
    ],
    'lte' => [
        'array' => ':attribute نباید بیشتر از :value آیتم داشته باشد.',
        'file' => 'حجم :attribute باید کمتر یا برابر با :value کیلوبایت باشد.',
        'numeric' => ':attribute باید کمتر یا برابر با :value باشد.',
        'string' => 'طول :attribute باید کمتر یا برابر با :value کاراکتر باشد.',
    ],
    'mac_address' => ':attribute باید یک آدرس MAC معتبر باشد.',
    'max' => [
        'array' => ':attribute نباید بیشتر از :max آیتم داشته باشد.',
        'file' => 'حجم :attribute نباید بزرگتر از :max کیلوبایت باشد.',
        'numeric' => ':attribute نباید بزرگتر از :max باشد.',
        'string' => ':attribute نباید بیشتر از :max کاراکتر باشد.',
    ],
    'max_digits' => ':attribute نباید بیشتر از :max رقم داشته باشد.',
    'mimes' => ':attribute باید یک فایل از نوع: :values باشد.',
    'mimetypes' => ':attribute باید یک فایل از نوع: :values باشد.',
    'min' => [
        'array' => ':attribute باید حداقل :min آیتم داشته باشد.',
        'file' => ':attribute باید حداقل :min کیلوبایت باشد.',
        'numeric' => ':attribute باید حداقل :min باشد.',
        'string' => ':attribute باید حداقل :min کاراکتر باشد.',
    ],
    'min_digits' => ':attribute باید حداقل :min رقم داشته باشد.',
    'missing' => ':attribute باید غایب باشد.',
    'missing_if' => ':attribute باید غایب باشد زمانی که :other برابر با :value است.',
    'missing_unless' => ':attribute باید غایب باشد مگر اینکه :other برابر با :value باشد.',
    'missing_with' => ':attribute باید غایب باشد زمانی که :values وجود دارد.',
    'missing_with_all' => ':attribute باید غایب باشد زمانی که :values وجود دارند.',
    'multiple_of' => ':attribute باید مضربی از :value باشد.',
    'not_in' => ':attribute انتخاب شده نامعتبر است.',
    'not_regex' => 'فرمت :attribute نامعتبر است.',
    'numeric' => ':attribute باید یک عدد باشد.',
    'password' => [
        'letters' => ':attribute باید حداقل یک حرف داشته باشد.',
        'mixed' => ':attribute باید حداقل یک حرف بزرگ و یک حرف کوچک داشته باشد.',
        'numbers' => ':attribute باید حداقل یک عدد داشته باشد.',
        'symbols' => ':attribute باید حداقل یک نماد داشته باشد.',
        'uncompromised' => ':attribute ارائه شده در یک نشت داده ظاهر شده است. لطفاً یک :attribute دیگر انتخاب کنید.',
    ],
    'present' => ':attribute باید وجود داشته باشد.',
    'present_if' => ':attribute باید وجود داشته باشد زمانی که :other برابر با :value است.',
    'present_unless' => ':attribute باید وجود داشته باشد مگر اینکه :other برابر با :value باشد.',
    'present_with' => ':attribute باید وجود داشته باشد زمانی که :values وجود دارد.',
    'present_with_all' => ':attribute باید وجود داشته باشد زمانی که :values وجود دارند.',
    'prohibited' => ':attribute ممنوع است.',
    'prohibited_if' => ':attribute ممنوع است زمانی که :other برابر با :value باشد.',
    'prohibited_unless' => ':attribute ممنوع است مگر اینکه :other در :values باشد.',
    'prohibits' => ':attribute مانع از وجود :other می‌شود.',
    'regex' => 'فرمت :attribute نامعتبر است.',
    'required' => ':attribute الزامی است.',
    'required_array_keys' => ':attribute باید شامل ورودی‌هایی برای :values باشد.',
    'required_if' => ':attribute زمانی که :other برابر با :value است الزامی است.',
    'required_if_accepted' => ':attribute زمانی که :other پذیرفته شده الزامی است.',
    'required_if_declined' => ':attribute زمانی که :other رد شده الزامی است.',
    'required_unless' => ':attribute الزامی است مگر اینکه :other در :values باشد.',
    'required_with' => ':attribute زمانی که :values وجود دارد الزامی است.',
    'required_with_all' => ':attribute زمانی که :values وجود دارند الزامی است.',
    'required_without' => ':attribute زمانی که :values وجود ندارد الزامی است.',
    'required_without_all' => ':attribute زمانی که هیچ‌یک از :values وجود ندارد الزامی است.',
    'same' => ':attribute باید با :other مطابقت داشته باشد.',
    'size' => [
        'array' => ':attribute باید شامل :size آیتم باشد.',
        'file' => ':attribute باید :size کیلوبایت باشد.',
        'numeric' => ':attribute باید برابر با :size باشد.',
        'string' => ':attribute باید شامل :size کاراکتر باشد.',
    ],
    'starts_with' => ':attribute باید با یکی از موارد زیر شروع شود: :values.',
    'string' => ':attribute باید یک رشته باشد.',
    'timezone' => ':attribute باید یک منطقه زمانی معتبر باشد.',
    'unique' => ':attribute قبلاً گرفته شده است.',
    'uploaded' => 'بارگذاری :attribute با شکست مواجه شد.',
    'uppercase' => ':attribute باید به صورت حروف بزرگ باشد.',
    'url' => ':attribute باید یک آدرس URL معتبر باشد.',
    'ulid' => ':attribute باید یک ULID معتبر باشد.',
    'uuid' => ':attribute باید یک UUID معتبر باشد.',


    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
