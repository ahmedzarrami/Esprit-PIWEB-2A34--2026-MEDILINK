<h2>Dashboard Admin</h2>

<table border="1">
<tr>
    <th>Date</th>
    <th>Heure</th>
</tr>

<?php foreach($rendezvous as $r): ?>
<tr>
    <td><?= $r['date'] ?></td>
    <td><?= $r['heure'] ?></td>
</tr>
<?php endforeach; ?>
</table>