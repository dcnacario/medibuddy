<!-- resources/views/schedules/index.blade.php -->

<h1>Schedules</h1>

@foreach ($schedules as $schedule)
<p>Date: {{ $schedule->date }}</p>
<ul>
    @foreach ($schedule->time_slots as $slot)
    <li>{{ $slot }}</li>
    @endforeach
</ul>
@endforeach