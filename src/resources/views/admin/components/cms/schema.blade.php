@props([
    'schema',     // array of field definitions
    'data',       // current data bag for this level
    'namePrefix', // e.g. "data[en]"
])

@php
    // Field types that render at half width and pair with the next half-width field.
    $halfWidthTypes = ['text', 'url', 'select', 'checkbox'];

    // Walk the schema and group consecutive half-width fields into pairs.
    $rows = [];
    $bucket = [];
    foreach ($schema as $field) {
        if (in_array($field['type'], $halfWidthTypes, true)) {
            $bucket[] = $field;
            if (count($bucket) === 2) {
                $rows[] = ['type' => 'pair', 'fields' => $bucket];
                $bucket = [];
            }
        } else {
            if ($bucket) {
                $rows[] = ['type' => 'pair', 'fields' => $bucket];
                $bucket = [];
            }
            $rows[] = ['type' => 'full', 'fields' => [$field]];
        }
    }
    if ($bucket) {
        $rows[] = ['type' => 'pair', 'fields' => $bucket];
    }
@endphp

@foreach($rows as $row)
    @if($row['type'] === 'pair' && count($row['fields']) > 1)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($row['fields'] as $f)
                <x-admin::cms.field :field="$f" :value="$data[$f['key']] ?? null" :name-prefix="$namePrefix" />
            @endforeach
        </div>
    @else
        @foreach($row['fields'] as $f)
            <x-admin::cms.field :field="$f" :value="$data[$f['key']] ?? null" :name-prefix="$namePrefix" />
        @endforeach
    @endif
@endforeach
