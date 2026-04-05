@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" class="brand-header-link">
{!! $slot !!}
</a>
</td>
</tr>
