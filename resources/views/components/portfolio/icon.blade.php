@props(['name', 'class' => ''])

@php
    $name = $name ?? '';
    $isUrl = str_starts_with($name, 'http');
    $isIconify = !$isUrl && str_contains($name, ':');
@endphp

@if($isUrl)
    <img src="{{ $name }}" class="{{ $class }} object-contain" alt="icon">
@elseif($isIconify)
    <iconify-icon icon="{{ $name }}" class="{{ $class }}" {{ $attributes }}></iconify-icon>
@else
    <span class="material-symbols-outlined {{ $class }}" {{ $attributes }}>{{ $name ?: 'question_mark' }}</span>
@endif