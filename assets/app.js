async function pollTicketStatus(ticketId) {
  const el = document.getElementById('ticket-status');
  if (!el) return;

  try {
    const res = await fetch(`api/queue_status.php?ticket_id=${encodeURIComponent(ticketId)}`, { credentials: 'same-origin' });
    const data = await res.json();

    if (!data.ok) {
      el.innerHTML = `<div class="flash error">${data.error || 'Unable to fetch status'}</div>`;
      return;
    }

    const t = data.ticket;
    const isTerminal = ['SERVED', 'NOSHOW', 'CANCELLED'].includes(t.status);
    el.innerHTML = `
      <div class="card">
        <div class="row">
          <div class="col">
            <h2>${t.service_name}</h2>
            <div><span class="badge">Ticket #${t.ticket_no}</span> <span class="badge">Status: ${t.status}</span></div>
            <small>Location: ${t.location}</small>
          </div>
          <div class="col">
            ${t.status === 'WAITING'
              ? `<div><b>People ahead:</b> ${t.ahead}</div><div><b>ETA:</b> ~${t.eta_minutes} min</div>`
              : `<div><b>ETA:</b> 0 min</div>`
            }
            <small>Joined: ${t.joined_at}</small>
          </div>
        </div>
      </div>
      ${isTerminal ? `
        <div class="card">
          <a class="btn" href="user.php">Back to Dashboard</a>
        </div>
      ` : ''}
    `;

    if (t.status === 'CALLED') {
      // Simple in-browser "notification"
      alert('You are being called now. Please go to the service desk.');
    }
  } catch (e) {
    console.error(e);
  }
}

function startPolling(ticketId, intervalMs=8000) {
  pollTicketStatus(ticketId);
  setInterval(() => pollTicketStatus(ticketId), intervalMs);
}
