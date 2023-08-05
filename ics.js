      // Function to get the current date in the format "yyyyMMdd"
    function getCurrentDate() {
      const now = new Date();
      const year = now.getFullYear();
      const month = padNumber(now.getMonth() + 1);
      const day = padNumber(now.getDate());
      return `${year}${month}${day}`;
    }

    function getNextWeekdayDate(weekday) {
      const weekdays = ['SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA'];
      const currentDay = new Date().getDay();
      const targetDay = weekdays.indexOf(weekday);
      let daysUntilTargetDay = targetDay - currentDay;

      if (daysUntilTargetDay <= 0) {
        daysUntilTargetDay += 7;
      }

      const nextDate = new Date();
      nextDate.setDate(nextDate.getDate() + daysUntilTargetDay);

      const year = nextDate.getFullYear();
      const month = padNumber(nextDate.getMonth() + 1);
      const day = padNumber(nextDate.getDate());

      return `${year}${month}${day}`;
    }

    // Function to format number with leading zero
    function padNumber(number) {
      return number.toString().padStart(2, '0');
    }

    // Function to format date as per .ics format (yyyyMMdd)
    function formatDate(dateString) {
      if (!dateString) return '';

      const [day, month, year] = dateString.split('-');
      return `${year}${month}${day}`;
    }

    // Function to generate .ics time format from "hh:mm AM/PM" time string (HHmmss)
    function getICSTime(timeString) {
      if (!timeString) return '';

      // Remove "AM" or "PM" if present
      // timeString = timeString.replace(/ AM| PM/g, '');

      const [time, period] = timeString.split(' ');
      const [hours, minutes] = time.split(':');

      let hours24 = Number(hours);
      if (period === 'PM' && hours24 < 12) {
        hours24 += 12;
      }
      hours24 = padNumber(hours24);

      return `${hours24}${minutes}00`;
    }

    // Function to generate .ics end time format by adding one hour to the start time (HHmmss)
    function getICSEndTime(timeString) {
      if (!timeString) return '';

      const [hours, minutes] = timeString.split(':');
      const paddedHours = padNumber(Number(hours) + 1);
      return `${paddedHours}${minutes}00`;
    }

    // Function to generate .ics file
    function generateICSContent(batch) {
      const calendarData = [
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//Thapar//ClassSchedule//EN',
      ];

        batch.schedule.forEach((item) => {
          const {
            details,
            time,
            week,
          } = item;

          // const [subject, location] = details.split(' ');
          var subject = bestSubjectCodesFromSchedule(details);
          const finalLoc = removeSubjectCodesFromSchedule(details);
          const [startTime, endTime] = time.split(' - ');

          const startDate = formatDate(week);
          const startTimeICS = getICSTime(startTime);
          const endTimeICS = getICSTime(endTime);
          const weekCode = week.substring(0, 2).toUpperCase();
          
          const eventContent = [
            'BEGIN:VEVENT',
            `SUMMARY:${subject}`,
            `LOCATION:${finalLoc}`,
            `DESCRIPTION:${details}`,
            `RRULE:FREQ=WEEKLY;BYDAY=${weekCode};WKST=SU`,
            `DTSTART:${getNextWeekdayDate(weekCode)}T${startTimeICS}`,
            `DTEND:${getNextWeekdayDate(weekCode)}T${endTimeICS}`,
            'END:VEVENT',
          ];

          calendarData.push(...eventContent);
        });

      calendarData.push('END:VCALENDAR');

      return calendarData.join('\n');
    }




function bestSubjectCodesFromSchedule(subjectString) {
    const bestSubRegex = /[UP][A-Za-z]{2}\d{3}/g;
  const afteritRegex =/(?<=[UP][A-Za-z]{2}\d{3}\b).*/gm;

  // const result = subjectString.replace(locRegex, '');

  var matches = subjectString.match(afteritRegex);
  
  if (matches) {
    for (const match of matches) {
      subjectString = subjectString.replace(match, '');
    }
  }
  
    return subjectString.trim();

}


function removeSubjectCodesFromSchedule(subjectString) {
  const bestSubRegex = /[UP][A-Za-z]{2}\d{3}/g;
  const locRegex = /^.*?(?=\b[UP][A-Za-z]{2}\d{3}\b)/gm;

  // const result = subjectString.replace(locRegex, '');

  var matches = subjectString.match(locRegex);
  
  if (matches) {
    for (const match of matches) {
      subjectString = subjectString.replace(match, '');
    }
  }

    matches = subjectString.match(bestSubRegex);
  
  if (matches) {
    for (const match of matches) {
      subjectString = subjectString.replace(match, '');
    }
  }
  
  const trimmedString = subjectString.trim();
  const trimmedStringWithoutLeadingSlashes = trimmedString.replace(/^\/+/, '');
    const stringWithoutCodes = trimmedStringWithoutLeadingSlashes.replace(/\b(Lecture|Practical|Tutorial|Elective)\b/g, '');

  return stringWithoutCodes.trim();
}





function getParamsFromURL() {
  const urlParams = new URLSearchParams(window.location.search);
  const params = {};

  for (const [key, value] of urlParams) {
    params[key] = value;
  }

  return params;
}
