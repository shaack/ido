SELECT tt.created as Start, tt.duration as 'Dauer (h)', sv.name as Leistung, ts.name as Task, pr.name as Project
FROM time_trackings tt
         LEFT JOIN tasks ts ON ts.id = tt.task_id
         LEFT JOIN services sv ON ts.service_id = sv.id
        LEFT JOIN projects pr ON sv.project_id = pr.id
WHERE pr.id = 379 AND tt.duration > 0 ORDER by start

# Dateiname "FUZ-Zeiterfassung 2023-12.xlsx"