Update columns

```sql
UPDATE customers set current=1 WHERE current = 'Ja';
UPDATE customers set current=0 WHERE current = 'Nein' OR current IS NULL OR current = '';

UPDATE projects SET project_status_id = 1 WHERE status = 'Angefragt';
UPDATE projects SET project_status_id = 5 WHERE status = 'Angebot gesendet';
UPDATE projects SET project_status_id = 2 WHERE status = 'Läuft';
UPDATE projects SET project_status_id = 14 WHERE status = 'Fertig, Rechnung stellen';
UPDATE projects SET project_status_id = 10 WHERE status = 'Rechnung gesendet';
UPDATE projects SET project_status_id = 13 WHERE status = 'Rechnung angemahnt';
UPDATE projects SET project_status_id = 4 WHERE status = 'Zurückgestellt';
UPDATE projects SET project_status_id = 3 WHERE status = 'Beendet, Rechnung bezahlt';
UPDATE projects SET project_status_id = 16 WHERE status = 'Rechnung Storniert';
UPDATE projects SET project_status_id = 20 WHERE status = 'Beendet, Kostenlos';
UPDATE projects SET project_status_id = 18 WHERE status = 'Nicht beauftragt';

UPDATE services SET created = created_date;

UPDATE tasks set desktop=1 WHERE desktop = 'Ja';
UPDATE tasks set desktop=0 WHERE desktop = 'Nein' OR desktop IS NULL OR desktop = '';

UPDATE tasks set prio=1 WHERE prio = 'Hoch';
UPDATE tasks set prio=0 WHERE prio = 'Normal' OR prio IS NULL OR prio = '';

UPDATE tasks set status=1 WHERE status = 'Erledigt';
UPDATE tasks set status=0 WHERE status = 'ToDo';
```

Check for constraints

```sql
SELECT time_tracking.*
FROM time_tracking
LEFT JOIN tasks
  ON tasks.id = time_tracking.task_id
WHERE tasks.id IS NULL
```
