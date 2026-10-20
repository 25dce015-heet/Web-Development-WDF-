let allEvents = [];

document.addEventListener("DOMContentLoaded", function () {
    fetchEvents();
});

function showAlert(message, type) {
    const box = document.getElementById("alertBox");
    box.className = "alert-box " + type;
    box.textContent = message;
    box.style.display = "block";
    setTimeout(() => {
        box.style.display = "none";
    }, 4000);
}

function fetchEvents() {
    const loading = document.getElementById("loadingIndicator");
    const table = document.getElementById("eventsTable");
    loading.style.display = "block";
    table.style.display = "none";

    fetch("api.php?action=list")
        .then(response => response.json())
        .then(res => {
            loading.style.display = "none";
            if (res.success) {
                allEvents = res.data;
                renderEvents(allEvents);
                table.style.display = "table";
            } else {
                showAlert(res.error || "Failed to load events.", "danger");
            }
        })
        .catch(err => {
            loading.style.display = "none";
            showAlert("Network or server error while fetching events.", "danger");
        });
}

function renderEvents(events) {
    const tbody = document.getElementById("eventsBody");
    tbody.innerHTML = "";

    if (events.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding:20px; color:#888;">No events found.</td></tr>';
        return;
    }

    events.forEach(ev => {
        const tr = document.createElement("tr");

        const badgeClass = ev.status === "Upcoming" ? "badge-upcoming" : "badge-completed";

        tr.innerHTML = `
            <td>${ev.id}</td>
            <td><strong>${escapeHtml(ev.event_title)}</strong></td>
            <td>${ev.event_date}<br><small style="color:#666;">${ev.event_time}</small></td>
            <td>${escapeHtml(ev.venue)}</td>
            <td>${escapeHtml(ev.category)}</td>
            <td><span class="badge ${badgeClass}">${escapeHtml(ev.status)}</span></td>
            <td>
                <button class="btn btn-warning btn-sm" onclick="editEvent(${ev.id})">Edit</button>
                <button class="btn btn-danger btn-sm" onclick="deleteEvent(${ev.id})">Delete</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function filterEvents() {
    const query = document.getElementById("searchInput").value.toLowerCase();
    const filtered = allEvents.filter(ev => {
        return ev.event_title.toLowerCase().includes(query) ||
               ev.venue.toLowerCase().includes(query) ||
               ev.category.toLowerCase().includes(query);
    });
    renderEvents(filtered);
}

function openAddModal() {
    document.getElementById("modalTitle").textContent = "Add New Event";
    document.getElementById("eventId").value = "";
    document.getElementById("eventForm").reset();
    document.getElementById("eventModal").classList.add("active");
}

function closeModal() {
    document.getElementById("eventModal").classList.remove("active");
}

function editEvent(id) {
    fetch(`api.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                const ev = res.data;
                document.getElementById("modalTitle").textContent = "Edit Event #" + ev.id;
                document.getElementById("eventId").value = ev.id;
                document.getElementById("eventTitle").value = ev.event_title;
                document.getElementById("eventDate").value = ev.event_date;
                document.getElementById("eventTime").value = ev.event_time;
                document.getElementById("eventVenue").value = ev.venue;
                document.getElementById("eventCategory").value = ev.category;
                document.getElementById("eventStatus").value = ev.status;
                document.getElementById("eventDesc").value = ev.description;
                document.getElementById("eventModal").classList.add("active");
            } else {
                showAlert(res.error || "Event not found.", "danger");
            }
        })
        .catch(() => showAlert("Failed to fetch event details.", "danger"));
}

function handleFormSubmit(e) {
    e.preventDefault();

    const id = document.getElementById("eventId").value;
    const payload = {
        event_title: document.getElementById("eventTitle").value.trim(),
        event_date: document.getElementById("eventDate").value,
        event_time: document.getElementById("eventTime").value,
        venue: document.getElementById("eventVenue").value.trim(),
        category: document.getElementById("eventCategory").value,
        status: document.getElementById("eventStatus").value,
        description: document.getElementById("eventDesc").value.trim()
    };

    const isUpdate = id !== "";
    const url = isUpdate ? `api.php?action=update&id=${id}` : "api.php?action=create";

    fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            closeModal();
            showAlert(data.message || "Operation successful!", "success");
            fetchEvents();
        } else {
            showAlert(data.error || "Error occurred.", "danger");
        }
    })
    .catch(() => showAlert("Request failed.", "danger"));
}

function deleteEvent(id) {
    if (!confirm("Are you sure you want to delete this event?")) {
        return;
    }

    fetch(`api.php?action=delete&id=${id}`, {
        method: "POST"
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message || "Event deleted successfully.", "success");
            fetchEvents();
        } else {
            showAlert(data.error || "Failed to delete event.", "danger");
        }
    })
    .catch(() => showAlert("Request failed while deleting.", "danger"));
}

function escapeHtml(text) {
    if (!text) return "";
    return text.replace(/[&<>"']/g, function (m) {
        return ({
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;"
        })[m];
    });
}
