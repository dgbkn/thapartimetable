    // Function to generate .ics file content for a batch
    function generateICSContent(batch) {
      let icsContent = `BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Your Company//Your App//EN
CALSCALE:GREGORIAN`;

      batch.schedule.forEach((entry) => {
        icsContent += `
BEGIN:VEVENT
UID:${Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)}
DTSTAMP:${formatDate(new Date())}
SUMMARY:${entry.details}
DTSTART:${getICSTime(entry.time)}
DTEND:${getICSEndTime(entry.time)}
RRULE:FREQ=WEEKLY;BYDAY=${entry.week.substring(0, 2).toUpperCase()}
END:VEVENT`;
      });

      icsContent += `
END:VCALENDAR`;

      return icsContent;
    }

    // Function to format date as per .ics format
    function formatDate(date) {
      const year = date.getUTCFullYear();
      const month = padNumber(date.getUTCMonth() + 1);
      const day = padNumber(date.getUTCDate());
      const hours = padNumber(date.getUTCHours());
      const minutes = padNumber(date.getUTCMinutes());
      const seconds = padNumber(date.getUTCSeconds());
      return `${year}${month}${day}T${hours}${minutes}${seconds}Z`;
    }

    // Function to pad number with leading zero
    function padNumber(number) {
      return String(number).padStart(2, '0');
    }

    // Function to get .ics time format from "hh:mm AM/PM" time string
    function getICSTime(timeString) {
      const [time, period] = timeString.split(' ');
      const [hours, minutes] = time.split(':');
      const paddedHours = period === 'AM' ? padNumber(hours) : padNumber(Number(hours) + 12);
      return `${paddedHours}${minutes}00`;
    }

    // Function to get .ics end time format by adding one hour to the start time
    function getICSEndTime(timeString) {
      const [hours, minutes] = timeString.split(':');
      const paddedHours = padNumber(Number(hours) + 1);
      return `${paddedHours}${minutes}00`;
    }



function getParamsFromURL() {
  const urlParams = new URLSearchParams(window.location.search);
  const params = {};

  for (const [key, value] of urlParams) {
    params[key] = value;
  }

  return params;
}
