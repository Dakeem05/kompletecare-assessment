<x-mail::message>
# Site Down Alert

The monitor **{{ $monitor->url }}** is currently down.

**URL:** [{{ $monitor->url }}]({{ $monitor->url }})
**Checked At:** {{ $monitor->last_checked_at }}
**Consecutive Failures:** {{ $monitor->consecutive_failures }}

Please check the site immediately.

<x-mail::button :url="$monitor->url">
View Site
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
