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

    'accepted' => ':attribute 항목을 동의해야 합니다.',
    'accepted_if' => ':other 항목이 :value 일 때 :attribute 항목을 동의해야 합니다.',
    'active_url' => ':attribute 항목은 올바른 URL이 아닙니다.',
    'after' => ':attribute 항목은 :date 이후의 날짜여야 합니다.',
    'after_or_equal' => ':attribute 항목은 :date 이후이거나 같은 날짜여야 합니다.',
    'alpha' => ':attribute 항목은 문자만 포함할 수 있습니다.',
    'alpha_dash' => ':attribute 항목은 문자, 숫자, 대시(-), 밑줄(_)만 포함할 수 있습니다.',
    'alpha_num' => ':attribute 항목은 문자와 숫자만 포함할 수 있습니다.',
    'array' => ':attribute 항목은 배열이어야 합니다.',
    'before' => ':attribute 항목은 :date 이전의 날짜여야 합니다.',
    'before_or_equal' => ':attribute 항목은 :date 이전이거나 같은 날짜여야 합니다.',
    'between' => [
        'numeric' => ':attribute 항목은 :min 에서 :max 사이여야 합니다.',
        'file' => ':attribute 항목은 :min 에서 :max 킬로바이트 사이여야 합니다.',
        'string' => ':attribute 항목은 :min 자에서 :max 자 사이여야 합니다.',
        'array' => ':attribute 항목은 :min 개에서 :max 개 사이여야 합니다.',
    ],
    'boolean' => ':attribute 필드는 true 또는 false여야 합니다.',
    'confirmed' => ':attribute 확인 값이 일치하지 않습니다.',
    'current_password' => '비밀번호가 올바르지 않습니다.',
    'date' => ':attribute 항목은 올바른 날짜가 아닙니다.',
    'date_equals' => ':attribute 항목은 :date 와 같은 날짜여야 합니다.',
    'date_format' => ':attribute 항목이 :format 형식과 일치하지 않습니다.',
    'different' => ':attribute 항목과 :other 항목은 서로 달라야 합니다.',
    'digits' => ':attribute 항목은 :digits 자리여야 합니다.',
    'digits_between' => ':attribute 항목은 :min 자리에서 :max 자리 사이여야 합니다.',
    'dimensions' => ':attribute 항목의 이미지 크기가 올바르지 않습니다.',
    'distinct' => ':attribute 필드에 중복된 값이 있습니다.',
    'email' => ':attribute 항목은 올바른 이메일 주소여야 합니다.',
    'ends_with' => ':attribute 항목은 다음 중 하나로 끝나야 합니다: :values.',
    'exists' => '선택한 :attribute 항목이 유효하지 않습니다.',
    'file' => ':attribute 항목은 파일이어야 합니다.',
    'filled' => ':attribute 필드는 값이 있어야 합니다.',
    'gt' => [
        'numeric' => ':attribute 항목은 :value 보다 커야 합니다.',
        'file' => ':attribute 항목은 :value 킬로바이트보다 커야 합니다.',
        'string' => ':attribute 항목은 :value 자보다 커야 합니다.',
        'array' => ':attribute 항목은 :value 개보다 많아야 합니다.',
    ],
    'gte' => [
        'numeric' => ':attribute 항목은 :value 이상이어야 합니다.',
        'file' => ':attribute 항목은 :value 킬로바이트 이상이어야 합니다.',
        'string' => ':attribute 항목은 :value 자 이상이어야 합니다.',
        'array' => ':attribute 항목은 :value 개 이상이어야 합니다.',
    ],
    'image' => ':attribute 항목은 이미지여야 합니다.',
    'in' => '선택한 :attribute 항목이 유효하지 않습니다.',
    'in_array' => ':attribute 필드가 :other 에 존재하지 않습니다.',
    'integer' => ':attribute 항목은 정수여야 합니다.',
    'ip' => ':attribute 항목은 올바른 IP 주소여야 합니다.',
    'ipv4' => ':attribute 항목은 올바른 IPv4 주소여야 합니다.',
    'ipv6' => ':attribute 항목은 올바른 IPv6 주소여야 합니다.',
    'json' => ':attribute 항목은 올바른 JSON 문자열이어야 합니다.',
    'lt' => [
        'numeric' => ':attribute 항목은 :value 보다 작아야 합니다.',
        'file' => ':attribute 항목은 :value 킬로바이트보다 작아야 합니다.',
        'string' => ':attribute 항목은 :value 자보다 작아야 합니다.',
        'array' => ':attribute 항목은 :value 개보다 적어야 합니다.',
    ],
    'lte' => [
        'numeric' => ':attribute 항목은 :value 이하여야 합니다.',
        'file' => ':attribute 항목은 :value 킬로바이트 이하여야 합니다.',
        'string' => ':attribute 항목은 :value 자 이하여야 합니다.',
        'array' => ':attribute 항목은 :value 개를 초과할 수 없습니다.',
    ],
    'max' => [
        'numeric' => ':attribute 항목은 :max 보다 클 수 없습니다.',
        'file' => ':attribute 항목은 :max 킬로바이트보다 클 수 없습니다.',
        'string' => ':attribute 항목은 :max 자보다 클 수 없습니다.',
        'array' => ':attribute 항목은 :max 개를 초과할 수 없습니다.',
    ],
    'mimes' => ':attribute 항목은 다음 형식의 파일이어야 합니다: :values.',
    'mimetypes' => ':attribute 항목은 다음 형식의 파일이어야 합니다: :values.',
    'min' => [
        'numeric' => ':attribute 항목은 최소 :min 이어야 합니다.',
        'file' => ':attribute 항목은 최소 :min 킬로바이트여야 합니다.',
        'string' => ':attribute 항목은 최소 :min 자여야 합니다.',
        'array' => ':attribute 항목은 최소 :min 개여야 합니다.',
    ],
    'multiple_of' => ':attribute 항목은 :value 의 배수여야 합니다.',
    'not_in' => '선택한 :attribute 항목이 유효하지 않습니다.',
    'not_regex' => ':attribute 형식이 유효하지 않습니다.',
    'numeric' => ':attribute 항목은 숫자여야 합니다.',
    'password' => '비밀번호가 올바르지 않습니다.',
    'present' => ':attribute 필드가 존재해야 합니다.',
    'regex' => ':attribute 형식이 유효하지 않습니다.',
    'required' => ':attribute 필드는 필수입니다.',
    'required_if' => ':other 항목이 :value 일 때 :attribute 필드는 필수입니다.',
    'required_unless' => ':other 항목이 :values 에 포함되지 않는 한 :attribute 필드는 필수입니다.',
    'required_with' => ':values 항목이 있을 때 :attribute 필드는 필수입니다.',
    'required_with_all' => ':values 항목이 모두 있을 때 :attribute 필드는 필수입니다.',
    'required_without' => ':values 항목이 없을 때 :attribute 필드는 필수입니다.',
    'required_without_all' => ':values 항목이 모두 없을 때 :attribute 필드는 필수입니다.',
    'prohibited' => ':attribute 필드는 허용되지 않습니다.',
    'prohibited_if' => ':other 항목이 :value 일 때 :attribute 필드는 허용되지 않습니다.',
    'prohibited_unless' => ':other 항목이 :values 에 포함되지 않는 한 :attribute 필드는 허용되지 않습니다.',
    'same' => ':attribute 항목과 :other 항목이 일치해야 합니다.',
    'size' => [
        'numeric' => ':attribute 항목은 :size 여야 합니다.',
        'file' => ':attribute 항목은 :size 킬로바이트여야 합니다.',
        'string' => ':attribute 항목은 :size 자여야 합니다.',
        'array' => ':attribute 항목은 :size 개여야 합니다.',
    ],
    'starts_with' => ':attribute 항목은 다음 중 하나로 시작해야 합니다: :values.',
    'string' => ':attribute 항목은 문자열이어야 합니다.',
    'timezone' => ':attribute 항목은 올바른 시간대여야 합니다.',
    'unique' => ':attribute 항목은 이미 사용 중입니다.',
    'uploaded' => ':attribute 항목의 업로드에 실패했습니다.',
    'url' => ':attribute 항목은 올바른 URL이어야 합니다.',
    'uuid' => ':attribute 항목은 올바른 UUID여야 합니다.',

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
