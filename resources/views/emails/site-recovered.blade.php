<x-mail::message>
# Site Recovered

The monitor **{{ $monitor->url }}** has recovered and is now back online.

**URL:** [{{ $monitor->url }}]({{ $monitor->url }})
**Checked At:** {{ $monitor->last_checked_at }}

<x-mail::button :url="$monitor->url">
View Site
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
