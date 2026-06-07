@props([
    'name'       => 'mobile',
    'codeName'   => 'mobile_country_code',
    'codeValue'  => '',
    'mobileValue'=> '',
    'required'   => false,
    'inputClass' => '',
    'id'         => null,
])
@php
$inputId  = $id ?? $name;
$dialCodes = [
    ['code' => 'AF', 'dial' => '+93',  'name' => 'Afghanistan'],
    ['code' => 'AL', 'dial' => '+355', 'name' => 'Albania'],
    ['code' => 'DZ', 'dial' => '+213', 'name' => 'Algeria'],
    ['code' => 'AR', 'dial' => '+54',  'name' => 'Argentina'],
    ['code' => 'AU', 'dial' => '+61',  'name' => 'Australia'],
    ['code' => 'AT', 'dial' => '+43',  'name' => 'Austria'],
    ['code' => 'BD', 'dial' => '+880', 'name' => 'Bangladesh'],
    ['code' => 'BE', 'dial' => '+32',  'name' => 'Belgium'],
    ['code' => 'BR', 'dial' => '+55',  'name' => 'Brazil'],
    ['code' => 'CA', 'dial' => '+1',   'name' => 'Canada'],
    ['code' => 'CL', 'dial' => '+56',  'name' => 'Chile'],
    ['code' => 'CN', 'dial' => '+86',  'name' => 'China'],
    ['code' => 'CO', 'dial' => '+57',  'name' => 'Colombia'],
    ['code' => 'HR', 'dial' => '+385', 'name' => 'Croatia'],
    ['code' => 'CZ', 'dial' => '+420', 'name' => 'Czech Republic'],
    ['code' => 'DK', 'dial' => '+45',  'name' => 'Denmark'],
    ['code' => 'EG', 'dial' => '+20',  'name' => 'Egypt'],
    ['code' => 'FI', 'dial' => '+358', 'name' => 'Finland'],
    ['code' => 'FR', 'dial' => '+33',  'name' => 'France'],
    ['code' => 'DE', 'dial' => '+49',  'name' => 'Germany'],
    ['code' => 'GH', 'dial' => '+233', 'name' => 'Ghana'],
    ['code' => 'GR', 'dial' => '+30',  'name' => 'Greece'],
    ['code' => 'HU', 'dial' => '+36',  'name' => 'Hungary'],
    ['code' => 'IN', 'dial' => '+91',  'name' => 'India'],
    ['code' => 'ID', 'dial' => '+62',  'name' => 'Indonesia'],
    ['code' => 'IE', 'dial' => '+353', 'name' => 'Ireland'],
    ['code' => 'IL', 'dial' => '+972', 'name' => 'Israel'],
    ['code' => 'IT', 'dial' => '+39',  'name' => 'Italy'],
    ['code' => 'JP', 'dial' => '+81',  'name' => 'Japan'],
    ['code' => 'KE', 'dial' => '+254', 'name' => 'Kenya'],
    ['code' => 'MY', 'dial' => '+60',  'name' => 'Malaysia'],
    ['code' => 'MX', 'dial' => '+52',  'name' => 'Mexico'],
    ['code' => 'MA', 'dial' => '+212', 'name' => 'Morocco'],
    ['code' => 'NL', 'dial' => '+31',  'name' => 'Netherlands'],
    ['code' => 'NZ', 'dial' => '+64',  'name' => 'New Zealand'],
    ['code' => 'NG', 'dial' => '+234', 'name' => 'Nigeria'],
    ['code' => 'NO', 'dial' => '+47',  'name' => 'Norway'],
    ['code' => 'PK', 'dial' => '+92',  'name' => 'Pakistan'],
    ['code' => 'PE', 'dial' => '+51',  'name' => 'Peru'],
    ['code' => 'PH', 'dial' => '+63',  'name' => 'Philippines'],
    ['code' => 'PL', 'dial' => '+48',  'name' => 'Poland'],
    ['code' => 'PT', 'dial' => '+351', 'name' => 'Portugal'],
    ['code' => 'RO', 'dial' => '+40',  'name' => 'Romania'],
    ['code' => 'RU', 'dial' => '+7',   'name' => 'Russia'],
    ['code' => 'SA', 'dial' => '+966', 'name' => 'Saudi Arabia'],
    ['code' => 'SG', 'dial' => '+65',  'name' => 'Singapore'],
    ['code' => 'ZA', 'dial' => '+27',  'name' => 'South Africa'],
    ['code' => 'KR', 'dial' => '+82',  'name' => 'South Korea'],
    ['code' => 'ES', 'dial' => '+34',  'name' => 'Spain'],
    ['code' => 'SE', 'dial' => '+46',  'name' => 'Sweden'],
    ['code' => 'CH', 'dial' => '+41',  'name' => 'Switzerland'],
    ['code' => 'TW', 'dial' => '+886', 'name' => 'Taiwan'],
    ['code' => 'TH', 'dial' => '+66',  'name' => 'Thailand'],
    ['code' => 'TN', 'dial' => '+216', 'name' => 'Tunisia'],
    ['code' => 'TR', 'dial' => '+90',  'name' => 'Turkey'],
    ['code' => 'UA', 'dial' => '+380', 'name' => 'Ukraine'],
    ['code' => 'AE', 'dial' => '+971', 'name' => 'UAE'],
    ['code' => 'GB', 'dial' => '+44',  'name' => 'United Kingdom'],
    ['code' => 'US', 'dial' => '+1',   'name' => 'United States'],
    ['code' => 'UY', 'dial' => '+598', 'name' => 'Uruguay'],
    ['code' => 'VN', 'dial' => '+84',  'name' => 'Vietnam'],
];
$currentDial = old($codeName, $codeValue) ?: '+1';
$currentDial = preg_replace('/[^+\d]/', '', $currentDial);
$match = collect($dialCodes)->firstWhere('dial', $currentDial);
@endphp

<div class="phone-input-wrap" data-phone-wrap>

    <input type="hidden" name="{{ $codeName }}" id="{{ $inputId }}_code" value="{{ $currentDial }}">


    <div class="phone-input-group" data-phone-group>


        <button type="button" class="phone-code-btn" data-phone-trigger
                aria-haspopup="listbox" aria-expanded="false">
            <span class="phone-flag">{{ $match ? $match['code'] : '🌐' }}</span>
            <span class="phone-dial" data-phone-dial>{{ $currentDial }}</span>
            <svg class="phone-chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 8l4 4 4-4"/>
            </svg>
        </button>


        <input type="tel" id="{{ $inputId }}" name="{{ $name }}"
               value="{{ old($name, $mobileValue) }}"
               placeholder="{{ __('555 000 0000') }}"
               {{ $required ? 'required' : '' }}
               class="phone-number-input {{ $inputClass }}">
    </div>


    <div class="phone-dropdown hidden" data-phone-dropdown role="listbox">
        <div class="phone-search-wrap">
            <input type="text" placeholder="{{ __('Search country…') }}" class="phone-search" data-phone-search autocomplete="off">
        </div>
        <ul class="phone-list" data-phone-list>
            @foreach($dialCodes as $c)
            <li class="phone-option {{ $c['dial'] === $currentDial ? 'is-selected' : '' }}"
                data-dial="{{ $c['dial'] }}" data-name="{{ strtolower($c['name']) }}"
                role="option">
                <span class="phone-opt-flag">{{ $c['code'] }}</span>
                <span class="phone-opt-name">{{ $c['name'] }}</span>
                <span class="phone-opt-dial">{{ $c['dial'] }}</span>
            </li>
            @endforeach
        </ul>
    </div>
</div>
