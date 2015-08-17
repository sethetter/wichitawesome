BEGIN:VTIMEZONE
TZID:America/Chicago
X-LIC-LOCATION:America/Chicago
BEGIN:DAYLIGHT
TZOFFSETFROM:-0600
TZOFFSETTO:-0500
TZNAME:CDT
DTSTART:19700308T020000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:-0500
TZOFFSETTO:-0600
TZNAME:CST
DTSTART:19701101T020000
RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU
END:STANDARD
END:VTIMEZONE
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
CALSCALE:GREGORIAN
BEGIN:VEVENT
DTEND;TZID=America/Chicago:{{ $event->end_time->format('Ymd\THis') }}
UID:{{ $event->id }}
DTSTAMP;TZID=America/Chicago:{{ date('Ymd\THis', time()) }}
LOCATION:{{ $event->venue->address() }}
DESCRIPTION:{!! $event->description !!}
URL;VALUE=URI:{{ action('EventController@show', $event->id) }}
SUMMARY:{!! $event->name !!}
DTSTART;TZID=America/Chicago:{{ $event->start_time->format('Ymd\THis') }}
END:VEVENT
END:VCALENDAR