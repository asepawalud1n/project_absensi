@props(['url'])

<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block; font-size: 19px; font-weight: bold;
             text-decoration: none; color: #3d4852;">
            {{ config('app.name') }}
        </a>
    </td>
</tr>