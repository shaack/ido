SELECT tt.created as Start, tt.duration as 'Dauer (h)', ts.name as Task, sv.name as Leistung, pr.name as Project
FROM time_trackings tt
         LEFT JOIN tasks ts ON ts.id = tt.task_id
         LEFT JOIN services sv ON ts.service_id = sv.id
        LEFT JOIN projects pr ON sv.project_id = pr.id
WHERE pr.id = 365 ORDER by start