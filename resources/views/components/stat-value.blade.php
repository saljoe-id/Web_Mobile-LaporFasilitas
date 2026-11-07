@props(['value', 'label'])

<div class="stat-value"><strong>{{ number_format($value, 0, ',', '.') }}</strong><span>{{ $label }}</span></div>
