select DATE(created) date, cast(sum(duration) as decimal(4, 1)) as sum
from ido.time_trackings
group by CAST(created AS DATE)
ORDER BY date desc;