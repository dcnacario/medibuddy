<!-- resources/views/schedules/create.blade.php -->


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <title>Create Schedule</title>
</head>
<style>
    * {
        font-family: 'Poppins';
        margin: 0;
        padding: 0;
    }

    body {
        background-image: url('{{ asset(' images/lastcurve.png') }}');
        background-size: cover;
        background-repeat: no-repeat;
    }

    .navbar {
        background: #bfdbffff;
    }

    .navbar img {
        margin: 40px 40px 0px;
        width: 22%;
    }

    .center {
        background: #ffffffff;
        border-radius: 15px;
        margin: 60px 530px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .inputs {
        padding: 30px 30px;
    }

    .inputs label {
        font-size: 20px;
    }

    .inputs input {
        background: #bfdbffff;
        border-radius: 3px;
        border: 2px solid rgba(0, 0, 0, 0.5);
        height: 30px;
        width: 100%;
    }

    .inputs .put {
        margin-bottom: 10px;
    }

    .inputs .puto {
        margin-bottom: 5px;
    }

    .title {
        text-align: center;
        margin: 20px 250px;
    }

    .title h1 {
        color: #001f3fff;
        font-size: 35px;
        /* text-transform: uppercase; */
    }

    .sub-btn {
        display: flex;
        justify-content: center;
        margin-top: 5px;
    }

    .sub-btn button {
        color: white;
        cursor: pointer;
        border-radius: 5px;
        font-size: 18px;
        border: none;
        background: rgb(1, 67, 151, 0.9);
        height: 40px;
        padding: 5px 10px;
        transition: background 0.3s;
    }

    .sub-btn button:hover {
        background: #bfdbffff;
        color: black;
        border: 2px solid rgba(0, 0, 0, 0.5);
    }

    .setTime {
        font-size: 20px;
        margin: 0px 40px;
    }

    .oten {
        padding: 10px;
    }

    .bilat {
        background: rgb(0, 31, 63, 1);
        color: #ffffffff;
        margin: 10px 300px;
        padding: 5px 15px;
        display: flex;
        justify-content: space-between;
        border-radius: 10px;
        font-size: 10px;
    }

    /* Media query for mobile screens */
    @media (max-width: 768px) {
        body {
            background-image: url('{{ asset(' images/lastMobile.png') }}');
            background-position: center;
            background-repeat: no-repeat;
        }

        .navbar img {
            margin: 40px 40px 0px;
            width: 55%;
        }

        .title {
            text-align: center;
            padding: 10px;
            margin: 30px 20px 0px 20px;
        }

        .title h1 {
            color: #001f3fff;
            font-size: 20px;
        }

        .center {
            background: #ffffffff;
            border-radius: 15px;
            margin: 30px 35px 50px 35px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .inputs {
            padding: 20px 20px;
        }

        .inputs label {
            font-size: 20px;
        }

        .inputs input {
            background: #bfdbffff;
            border-radius: 3px;
            border: 2px solid rgba(0, 0, 0, 0.5);
            text-align: center;
            height: 40px;
            font-size: 18px;
            width: 100%;
        }

        .inputs .put {
            margin-bottom: 10px;
        }

        .inputs .puto {
            margin-bottom: 5px;
        }

        .inputs input[type="date"],
        .inputs input[type="time"] {
            width: 324px;
            color: #001f3fff;
            font-size: 18px;
            box-sizing: border-box;
        }

        .setTime {
            font-size: 13px;
            margin: 0px 40px;
        }

        .bilat {
            background: rgb(0, 31, 63, 1);
            color: #ffffffff;
            margin: 10px 30px;
            padding: 10px 15px;
            display: flex;
            justify-content: space-between;
            border-radius: 10px;
            font-size: 10px;
        }
    }
</style>

<body>
    <div class="navbar curved-background">
        <img src="{{ asset('images/medibuddyOriginal.png') }}" alt="Logo">
    </div>
    <div class="title">
        <h1>Optimize your health with a personalized medicine schedule for better adherence and well-being.</h1>
    </div>

    <div class="center">
        <div class="inputs">
            <form method="post" action="{{ route('schedules.store') }}">
                @if(session('message'))
                <p style="color: green; background-color: #dff0d8; border: 1px solid #d0e9c6; padding: 10px; margin-bottom: 15px;">{{ session('message') }}</p>
                @endif
                @if(session('error'))
                <p style="color: red; background-color: #f2dede; border: 1px solid #ebccd1; padding: 10px; margin-bottom: 15px;">{{ session('error') }}</p>
                @endif
                @csrf
                <label for="date">Date</label><br>
                <input class="put" type="date" name="date" required><br>

                @for ($i = 0; $i < 3; $i++) <label for="time_slots[]">Time Slot {{ $i + 1 }}</label><br>
                    <input class="puto" type="text" placeholder="Add Label" name="label_time_slots[{{ $i }}]" required><br>
                    <input class="put" type="time" name="time_slots[{{ $i }}]" required><br>
                    @endfor

                    <div class="sub-btn">
                        <button type="submit">Create Schedule</button>
                    </div>
            </form>
        </div>
    </div>


    <!-- ----PARAS TABLE SA UPCOMMING NGA NA SET NGA TIME----- -->
    <div class="setTime">
        <h1>Schedule for Today</h1>
        <hr>
    </div>

    <div class="oten">
        @foreach ($scheduleForToday as $timeSlot)
        <div class="bilat">
            <div>
                <h1>{{ $timeSlot['label'] ?? 'No Label' }}</h1>
            </div>
            <div>
                <h1>{{ $timeSlot['time'] ?? 'No Time' }}</h1>
            </div>
        </div>
        @endforeach
    </div>



    <!-- ----PARAS TABLE SA HISTORY NGA NA SET NGA TIME----- -->
    <div class="setTime">
        <h1>History Set Time</h1>
        <hr>
    </div>

    <div class="oten">
        @foreach ($allSchedules as $schedule)
        <div class="bilat">
            <div>
                <h1>{{ $schedule->date }}</h1>
            </div>
            <div>
                @foreach ($schedule->time_slots as $timeSlot)
                <h1>{{ $timeSlot['label'] ?? 'No Label' }} - {{ $timeSlot['time'] ?? 'No Time' }}</h1>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

</body>

</html>