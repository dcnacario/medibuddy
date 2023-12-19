<!-- resources/views/schedules/create.blade.php -->

<h1>Create Schedule</h1>

@if(session('message'))
<p>{{ session('message') }}</p>
@endif

<form method="post" action="{{ route('schedules.store') }}">
    @csrf
    <label for="date">Date:</label>
    <input type="date" name="date" required>
    <label for="time_slots[]">Time Slot 1:</label>
    <input type="time" name="time_slots[]" required>
    <label for="time_slots[]">Time Slot 2:</label>
    <input type="time" name="time_slots[]" required>
    <label for="time_slots[]">Time Slot 3:</label>
    <input type="time" name="time_slots[]" required>
    <button type="submit">Create Schedule</button>
</form>