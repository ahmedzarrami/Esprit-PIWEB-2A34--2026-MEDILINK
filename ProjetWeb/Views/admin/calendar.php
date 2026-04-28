<!DOCTYPE html>
<html>
<head>
<title>Calendrier RDV</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<style>
body { background:#eef2f7; }

.sidebar {
    width:250px;
    height:100vh;
    background:#2F80ED;
    color:white;
    position:fixed;
    padding:20px;
}

.main {
    margin-left:260px;
    padding:30px;
}
</style>
</head>

<body>

<div class="sidebar">
    <h3>MediLink</h3>
    <p>📅 Rendez-vous</p>
    <p>👥 Patients</p>
</div>

<div class="main">

<h2>Agenda</h2>

<div id="calendar"></div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        events: [
            {
                title: 'RDV - Patient 1',
                start: '2026-04-10T10:00:00'
            },
            {
                title: 'RDV - Patient 2',
                start: '2026-04-11T14:00:00'
            }
        ]
    });

    calendar.render();
});
</script>

</body>
</html>