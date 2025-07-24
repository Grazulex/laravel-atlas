{{-- Policy Card Component --}}
@include("exports.partials.common.card-wrapper", ["class" => ""])
    @include("exports.partials.common.card-header", [
        "icon" => "🛡️",
        "title" => $item["name"],
        "subtitle" => $item["namespace"],
        "badges" => [
            [
                "text" => "Policy",
                "class" => "bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300",
                "icon" => "🛡️"
            ]
        ]
    ])

    <div class="p-6 space-y-6">
        {{-- Basic Information --}}
        <div class="grid md:grid-cols-2 gap-4">
            @include("exports.partials.common.property-item", [
                "label" => "File Location",
                "value" => str_replace(base_path() . "/", "", $item["file"]),
                "type" => "code"
            ])

            @include("exports.partials.common.property-item", [
                "label" => "Class",
                "value" => $item["class"],
                "type" => "code"
            ])
        </div>

        {{-- Flow Section --}}
        @if (!empty($item["flow"]))
            @include("exports.partials.common.flow-section", [
                "flow" => $item["flow"],
                "type" => "policy"
            ])
        @endif
    </div>
@endinclude
