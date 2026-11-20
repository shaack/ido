/**
 * Erledigt und Markieren ohne Neuladen. Fängt Klicks auf die Links
 * /tasks/done/{id}?done=… und /tasks/marked/{id}?marked=… ab, schickt sie per
 * fetch und schaltet danach Zeile, Icon und Link um. Gilt für die Taskliste
 * (tr.done-true, tr.task-marked) und das Dashboard (li.is-done, li.is-marked).
 * Schlägt die Anfrage fehl, wird dem Link normal gefolgt.
 */
document.addEventListener("click", async (event) => {
    const link = event.target.closest('a[href^="/tasks/done/"], a[href^="/tasks/marked/"]')
    if (!link || event.metaKey || event.ctrlKey || event.shiftKey || event.button !== 0) {
        return
    }
    event.preventDefault()
    if (link.dataset.busy) {
        return
    }
    link.dataset.busy = "1"
    try {
        const response = await fetch(link.href, {
            headers: {"X-Requested-With": "XMLHttpRequest", "Accept": "application/json"},
            credentials: "same-origin"
        })
        if (!response.ok) {
            throw new Error("HTTP " + response.status)
        }
        const task = await response.json()
        const row = link.closest("tr, li")
        const isDone = link.href.includes("/tasks/done/")
        const state = isDone ? task.done : task.marked

        // Zeile: Klassen der Taskliste und des Dashboards
        if (row) {
            if (isDone) {
                row.classList.toggle("done-true", state)
                row.classList.toggle("done-false", !state)
                row.classList.toggle("is-done", state)
            } else {
                row.classList.toggle("task-marked", state)
                row.classList.toggle("is-marked", state)
            }
        }

        // Icon: ausgefüllt, wenn erledigt bzw. markiert
        const icon = link.querySelector("i")
        if (icon) {
            icon.classList.toggle("fa-solid", state)
            icon.classList.toggle("fa-regular", !state)
        }

        // Der Link schaltet beim nächsten Klick zurück
        const url = new URL(link.href)
        url.searchParams.set(isDone ? "done" : "marked", state ? "0" : "1")
        link.href = url.pathname + url.search
        if (link.hasAttribute("aria-label")) {
            link.setAttribute("aria-label", isDone
                ? (state ? "Reopen task" : "Mark task done")
                : (state ? "Unpin task" : "Pin task"))
        }

        // Zahl der offenen Tasks in der Navigation
        if (typeof task.openTasks === "number") {
            for (const item of document.querySelectorAll('a[href="/tasks"]')) {
                let badge = item.querySelector(".app-nav-badge")
                if (task.openTasks > 0) {
                    if (!badge) {
                        badge = document.createElement("span")
                        badge.className = "app-nav-badge"
                        item.appendChild(badge)
                    }
                    badge.textContent = task.openTasks
                } else if (badge) {
                    badge.remove()
                }
            }
        }
    } catch (error) {
        console.warn("Task toggle failed, falling back to page load", error)
        window.location.href = link.href
    } finally {
        delete link.dataset.busy
    }
})
