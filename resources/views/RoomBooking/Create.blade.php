<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Booking</title>
</head>
<body>

    <h1>Room Booking System</h1>


    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form action="{{ route('roombooking.store') }}" method="post">
        @csrf
        <label for="room_name">Room Name:</label>
        <input type="text" name="room_name" id="room_name" required>

        <label for="user_name">User Name:</label>
        <input type="text" name="user_name" id="user_name" required>

        <label for="date">Date:</label>
        <input type="date" name="date" id="date" required>

        <button type="submit">Book Room</button>
    {{-- </form>
    @if(count($bookings) > 0)
        <ul>
            @foreach($bookings as $booking)
                <li>{{ $booking->room_name }} - {{ $booking->user_name }} - {{ $booking->date }}</li>
            @endforeach
        </ul> --}}
    @else
        <p>No bookings available.</p>
    @endif

    <!-- Your room booking form could go here -->

</body>
</html>
