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

    'accepted' => 'Atribut :attribute harus diterima.',
    'accepted_if' => 'Atribut :attribute harus diterima ketika :other adalah :value.',
    'active_url' => 'Atribut :attribute bukan URL yang valid.',
    'after' => 'Atribut :attribute harus tanggal setelah :date.',
    'after_or_equal' => 'Atribut :attribute harus tanggal setelah atau sama dengan :date.',
    'alpha' => 'Atribut :attribute hanya boleh berisi huruf.',
    'alpha_dash' => 'Atribut :attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'alpha_num' => 'Atribut :attribute hanya boleh berisi huruf dan angka.',
    'any_of' => 'The :attribute field is invalid.',
    'array' => 'Atribut :attribute harus berupa array.',
    'ascii' => 'The :attribute field must only contain single-byte alphanumeric characters and symbols.',
    'before' => 'Atribut :attribute harus tanggal sebelum :date.',
    'before_or_equal' => 'Atribut :attribute harus tanggal sebelum atau sama dengan :date.',
    'between' => [
        'array' => 'Atribut :attribute harus antara :min dan :max item.',
        'file' => 'Atribut :attribute harus antara :min dan :max kilobita.',
        'numeric' => 'Atribut :attribute harus antara :min dan :max.',
        'string' => 'Atribut :attribute harus antara :min dan :max karakter.',
    ],
    'boolean' => 'Bidang :attribute harus bernilai true atau false.',
    'can' => 'The :attribute field contains an unauthorized value.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'contains' => 'The :attribute field is missing a required value.',
    'current_password' => 'The password is incorrect.',
    'date' => 'Atribut :attribute bukan tanggal yang valid.',
    'date_equals' => 'Atribut :attribute harus tanggal yang sama dengan :date.',
    'date_format' => 'Atribut :attribute tidak cocok dengan format :format.',
    'decimal' => 'The :attribute field must have :decimal decimal places.',
    'declined' => 'Atribut :attribute harus ditolak.',
    'declined_if' => 'Atribut :attribute harus ditolak ketika :other adalah :value.',
    'different' => 'Atribut :attribute dan :other harus berbeda.',
    'digits' => 'Atribut :attribute harus :digits digit.',
    'digits_between' => 'Atribut :attribute harus antara :min dan :max digit.',
    'dimensions' => 'Atribut :attribute memiliki dimensi gambar yang tidak valid.',
    'distinct' => 'Bidang :attribute memiliki nilai duplikat.',
    'doesnt_contain' => 'The :attribute field must not contain any of the following: :values.',
    'doesnt_end_with' => 'The :attribute field must not end with one of the following: :values.',
    'doesnt_start_with' => 'The :attribute field must not start with one of the following: :values.',
    'email' => 'Atribut :attribute harus berupa alamat email yang valid.',
    'encoding' => 'The :attribute field must be encoded in :encoding.',
    'ends_with' => 'Atribut :attribute harus diakhiri dengan salah satu dari: :values.',
    'enum' => 'Atribut :attribute yang dipilih tidak valid.',
    'exists' => 'Atribut :attribute yang dipilih tidak valid.',
    'extensions' => 'The :attribute field must have one of the following extensions: :values.',
    'file' => 'Atribut :attribute harus berupa file.',
    'filled' => 'Bidang :attribute harus memiliki nilai.',
    'gt' => [
        'array' => 'Atribut :attribute harus memiliki lebih dari :value item.',
        'file' => 'Atribut :attribute harus lebih besar dari :value kilobita.',
        'numeric' => 'Atribut :attribute harus lebih besar dari :value.',
        'string' => 'Atribut :attribute harus lebih besar dari :value karakter.',
    ],
    'gte' => [
        'array' => 'Atribut :attribute harus memiliki :value item atau lebih.',
        'file' => 'Atribut :attribute harus lebih besar dari atau sama dengan :value kilobita.',
        'numeric' => 'Atribut :attribute harus lebih besar dari atau sama dengan :value.',
        'string' => 'Atribut :attribute harus lebih besar dari atau sama dengan :value karakter.',
    ],
    'hex_color' => 'The :attribute field must be a valid hexadecimal color.',
    'image' => 'Atribut :attribute harus berupa gambar.',
    'in' => 'Atribut :attribute yang dipilih tidak valid.',
    'in_array' => 'Bidang :attribute tidak ada di :other.',
    'in_array_keys' => 'The :attribute field must contain at least one of the following keys: :values.',
    'integer' => 'Atribut :attribute harus berupa bilangan bulat.',
    'ip' => 'Atribut :attribute harus berupa alamat IP yang valid.',
    'ipv4' => 'Atribut :attribute harus berupa alamat IPv4 yang valid.',
    'ipv6' => 'Atribut :attribute harus berupa alamat IPv6 yang valid.',
    'json' => 'Atribut :attribute harus berupa string JSON yang valid.',
    'list' => 'The :attribute field must be a list.',
    'lowercase' => 'The :attribute field must be lowercase.',
    'lt' => [
        'array' => 'Atribut :attribute harus memiliki kurang dari :value item.',
        'file' => 'Atribut :attribute harus kurang dari :value kilobita.',
        'numeric' => 'Atribut :attribute harus kurang dari :value.',
        'string' => 'Atribut :attribute harus kurang dari :value karakter.',
    ],
    'lte' => [
        'array' => 'Atribut :attribute tidak boleh memiliki lebih dari :value item.',
        'file' => 'Atribut :attribute harus kurang dari atau sama dengan :value kilobita.',
        'numeric' => 'Atribut :attribute harus kurang dari atau sama dengan :value.',
        'string' => 'Atribut :attribute harus kurang dari atau sama dengan :value karakter.',
    ],
    'mac_address' => 'Atribut :attribute harus berupa alamat MAC yang valid.',
    'max' => [
        'array' => 'Atribut :attribute tidak boleh memiliki lebih dari :max item.',
        'file' => 'Atribut :attribute tidak boleh lebih besar dari :max kilobita.',
        'numeric' => 'Atribut :attribute tidak boleh lebih besar dari :max.',
        'string' => 'Atribut :attribute tidak boleh lebih besar dari :max karakter.',
    ],
    'max_digits' => 'The :attribute field must not have more than :max digits.',
    'mimes' => 'Atribut :attribute harus berupa file tipe: :values.',
    'mimetypes' => 'Atribut :attribute harus berupa file tipe: :values.',
    'min' => [
        'array' => 'Atribut :attribute harus memiliki minimal :min item.',
        'file' => 'Atribut :attribute harus minimal :min kilobita.',
        'numeric' => 'Atribut :attribute harus minimal :min.',
        'string' => 'Atribut :attribute harus minimal :min karakter.',
    ],
    'min_digits' => 'The :attribute field must have at least :min digits.',
    'missing' => 'The :attribute field must be missing.',
    'missing_if' => 'The :attribute field must be missing when :other is :value.',
    'missing_unless' => 'The :attribute field must be missing unless :other is :value.',
    'missing_with' => 'The :attribute field must be missing when :values is present.',
    'missing_with_all' => 'The :attribute field must be missing when :values are present.',
    'multiple_of' => 'Atribut :attribute harus merupakan kelipatan dari :value.',
    'not_in' => 'Atribut :attribute yang dipilih tidak valid.',
    'not_regex' => 'Format atribut :attribute tidak valid.',
    'numeric' => 'Atribut :attribute harus berupa angka.',
    'password' => [
        'letters' => 'The :attribute field must contain at least one letter.',
        'mixed' => 'The :attribute field must contain at least one uppercase and one lowercase letter.',
        'numbers' => 'The :attribute field must contain at least one number.',
        'symbols' => 'The :attribute field must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.',
    ],
    'present' => 'Bidang :attribute harus ada.',
    'present_if' => 'The :attribute field must be present when :other is :value.',
    'present_unless' => 'The :attribute field must be present unless :other is :value.',
    'present_with' => 'The :attribute field must be present when :values is present.',
    'present_with_all' => 'The :attribute field must be present when :values are present.',
    'prohibited' => 'Bidang :attribute dilarang.',
    'prohibited_if' => 'Bidang :attribute dilarang ketika :other adalah :value.',
    'prohibited_if_accepted' => 'The :attribute field is prohibited when :other is accepted.',
    'prohibited_if_declined' => 'The :attribute field is prohibited when :other is declined.',
    'prohibited_unless' => 'Bidang :attribute dilarang kecuali :other ada di :values.',
    'prohibits' => 'Bidang :attribute melarang :other untuk ada.',
    'regex' => 'Format atribut :attribute tidak valid.',
    'required' => 'Bidang :attribute wajib diisi.',
    'required_array_keys' => 'Bidang :attribute harus berisi entri untuk: :values.',
    'required_if' => 'Bidang :attribute wajib diisi ketika :other adalah :value.',
    'required_if_accepted' => 'The :attribute field is required when :other is accepted.',
    'required_if_declined' => 'The :attribute field is required when :other is declined.',
    'required_unless' => 'Bidang :attribute wajib diisi kecuali :other ada di :values.',
    'required_with' => 'Bidang :attribute wajib diisi ketika :values ada.',
    'required_with_all' => 'Bidang :attribute wajib diisi ketika :values ada.',
    'required_without' => 'Bidang :attribute wajib diisi ketika :values tidak ada.',
    'required_without_all' => 'Bidang :attribute wajib diisi ketika tidak ada :values yang ada.',
    'same' => 'Atribut :attribute dan :other harus cocok.',
    'size' => [
        'array' => 'Atribut :attribute harus mengandung :size item.',
        'file' => 'Atribut :attribute harus berukuran :size kilobita.',
        'numeric' => 'Atribut :attribute harus berukuran :size.',
        'string' => 'Atribut :attribute harus berukuran :size karakter.',
    ],
    'starts_with' => 'Atribut :attribute harus dimulai dengan salah satu dari: :values.',
    'string' => 'Atribut :attribute harus berupa string.',
    'timezone' => 'Atribut :attribute harus berupa zona waktu yang valid.',
    'unique' => 'Atribut :attribute sudah digunakan.',
    'uploaded' => 'Atribut :attribute gagal diunggah.',
    'uppercase' => 'The :attribute field must be uppercase.',
    'url' => 'Format atribut :attribute tidak valid.',
    'ulid' => 'The :attribute field must be a valid ULID.',
    'uuid' => 'Atribut :attribute harus berupa UUID yang valid.',

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
